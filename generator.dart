import 'dart:io';

void main(List<String> args) {
  if (args.isEmpty) {
    print("Usage: dart generator.dart <table_name>");
    return;
  }

  var tableName = args[0];
  var modelName = _capitalize(_singularize(tableName));
  var apiControllerName = '${modelName}ApiController';

  _generateModel(modelName, tableName);
  _generateMigration(tableName); // Generate migration script
  _generateSeeder(tableName); // Generate migration script
  _generateApiController(apiControllerName, modelName, tableName);
  _generateApiRoutes(tableName, apiControllerName);

  print("API CRUD components for $tableName generated successfully!");
}

void _generateModel(String modelName, String tableName) {
  var modelContent = '''
<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class $modelName extends Model
{
    protected \$table = '$tableName';
    //protected \$fillable = ['column1', 'column2'];
}
''';

  _createFile('app/Models/$modelName.php', modelContent);
}

void _generateMigration(String tableName) {
  var migrationName = 'Create${_capitalize(tableName)}Table';
  var migrationContent = '''
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

class $migrationName extends Migration
{
    public function up()
    {
        Schema::create('$tableName', function (Blueprint \$table) {
            \$table->id();
            // Define your table columns here
            \$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('$tableName');
    }
}
''';

  _createMigrationFile('database/migrations', tableName, migrationContent);
}

void _generateSeeder(String tableName) {
  var migrationName = '${_capitalize(tableName)}TableSeeder';
  var className = _capitalize(_singularize(tableName));
  var migrationContent = '''
<?php
namespace Database\\Seeders;

use Illuminate\\Database\\Console\\Seeds\\WithoutModelEvents;
use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\DB;
use Illuminate\\Support\\Facades\Hash;
use App\\Models\\$className;

// php artisan db:seed --class=$migrationName

class $migrationName extends Seeder
{
    public function run(): void
    {
        $className::factory(10)->create();
    }
}

''';

  _createSeederFile('database/seeders', migrationName, migrationContent);
  _updateDatabaseSeederFile(migrationName);
}

void _updateDatabaseSeederFile(String seederClassName) {
  var databaseSeederPath = 'database/seeders/DatabaseSeeder.php';
  var file = File(databaseSeederPath);

  if (!file.existsSync()) {
    print("File $databaseSeederPath does not exist.");
    return;
  }

  var fileContent = file.readAsStringSync();
  if (fileContent.contains(seederClassName)) return;

  var lines = fileContent.split('\n');

  var seederUseStatement = "            $seederClassName::class,";

  var dontDeleteIndex =
      lines.indexOf("            //seeders @dont-delete-this-lines");
  lines.insert(dontDeleteIndex, seederUseStatement);

  // Write the updated content back to the file
  _createFile(databaseSeederPath, lines.join('\n'));
}

void _generateApiController(
    String controllerName, String modelName, String tableName) {
  var apiControllerContent = '''
<?php

namespace App\\Http\\Controllers;

use Illuminate\\Http\\Request;
use App\\Models\\$modelName;

class $controllerName extends Controller
{
    public function index()
    {
        \$limit = request()->input('limit', 10);
        \$items = $modelName::paginate(\$limit);
        \$data = \$items->items();
        \$meta = [
            'currentPage' => \$items->currentPage(),
            'perPage' => \$items->perPage(),
            'total' => \$items->total(),
        ];
        if (\$items->hasMorePages()) {
            \$meta['next_page_url'] = url(\$items->nextPageUrl());
        }
        if (\$items->currentPage() > 2) {
            \$meta['prev_page_url'] = url(\$items->previousPageUrl());
        }
        if (\$items->lastPage() > 1) {
            \$meta['last_page_url'] = url(\$items->url(\$items->lastPage()));
        }
        return response()->json(['data' => \$data, 'meta' => \$meta]);
    }

    public function store(Request \$request)
    {
        \$item = $modelName::create(\$request->all());
        return response()->json(['data' => \$item], 201);
    }

    public function show(\$id)
    {
        \$item = $modelName::findOrFail(\$id);
        return response()->json(['data' => \$item]);
    }

    public function update(Request \$request, \$id)
    {
        \$item = $modelName::findOrFail(\$id);
        \$item->update(\$request->all());
        return response()->json(['data' => \$item]);
    }

    public function destroy(\$id)
    {
        \$item = $modelName::findOrFail(\$id);
        \$item->delete();
        return response(null, 204);
    }
}
''';

  _createFile('app/Http/Controllers/$controllerName.php', apiControllerContent);
}

void _generateApiRoutes(String tableName, String controllerName) {
  var apiRoutesContent = '''
Route::prefix('$tableName')->middleware('auth:sanctum')->group(function () {
    Route::get('', [$controllerName::class, 'index']);
    Route::post('', [$controllerName::class, 'store']);
    Route::get('{id}', [$controllerName::class, 'show']);
    Route::put('{id}', [$controllerName::class, 'update']);
    Route::delete('{id}', [$controllerName::class, 'destroy']);
});
''';

  _appendToApiRoutes(apiRoutesContent, tableName, controllerName);
}

void _createFile(String path, String content) {
  var file = File(path);

  var mode = FileMode.write;
  file.writeAsStringSync(content, mode: mode);
}

String _capitalize(String text) {
  return text[0].toUpperCase() + text.substring(1);
}

String _singularize(String text) {
  if (text.endsWith('s')) {
    return text.substring(0, text.length - 1);
  }
  return text;
}

void _appendToApiRoutes(
    String content, String tableName, String controllerName) {
  var path = 'routes/api.php';
  var file = File(path);

  if (!file.existsSync()) {
    print(
        "File $path does not exist. Make sure the api.php file exists in the routes directory.");
    return;
  }

  var fileContent = file.readAsStringSync();
  var lines = fileContent.split('\n');

  // Add import statement at the top
  if (!_doesApiControllersExist(tableName, controllerName)) {
    lines.insert(1, "use App\\Http\\Controllers\\$controllerName;");
  }
  // Add the new content
  lines.add(content);
  _createFile(path, lines.join('\n'));
}

bool _doesApiRoutesExist(String tableName) {
  var path = 'routes/api.php';
  var file = File(path);

  if (!file.existsSync()) {
    return false;
  }

  var fileContent = file.readAsStringSync();
  return fileContent.contains("Route::prefix('$tableName')");
}

bool _doesApiControllersExist(String tableName, String controllerName) {
  var path = 'routes/api.php';
  var file = File(path);

  if (!file.existsSync()) {
    return false;
  }

  var fileContent = file.readAsStringSync();
  return fileContent.contains("use App\\Http\\Controllers\\$controllerName");
}

void _createMigrationFile(String directory, String tableName, String content) {
  // var timestamp = DateTime.now().millisecondsSinceEpoch;
  var filename = '2014_10_12_000000_create_${tableName}_table.php';
  var migrationPath = '$directory/$filename';

  if (File(migrationPath).existsSync()) return;
  _createFile(migrationPath, content);
}

void _createSeederFile(String directory, String className, String content) {
  var filename = '${className}.php';
  var migrationPath = '$directory/$filename';
  if (File(migrationPath).existsSync()) return;
  _createFile(migrationPath, content);
}
