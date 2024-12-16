<?php

namespace Brackets\CraftablePro\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class GeneratePermissionTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'craftable-pro:generate-permission-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate permission translations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! File::exists(resource_path('translations'))) {
            File::makeDirectory(resource_path('translations'));
        }

        if (! File::exists(resource_path('translations/permissions'))) {
            File::makeDirectory(resource_path('translations/permissions'));
        }

        $translatedPermissionsFilePath = resource_path('translations/permissions') . '/permission_translations.json';

        // Generate permissions default translations
        $permissions = collect(Permission::all()->map->name)->mapWithKeys(function ($permission) {
            $name = '';
            $permissionName = collect();

            collect(explode('.', $permission))->each(function ($value, $key) use (&$name, &$permissionName) {
                $name .= $key == 0 ? $value : ".$value";

                $permissionName->put($name, $name);
            });

            return $permissionName->all();
        });

        // Translate generated permissions
        $translatedPermissions = collect();
        $permissions->each(function ($permission) use ($translatedPermissions) {
            if (! $translatedPermissions->has($permission)) {
                $translatedPermissions->put($permission, ['en' => $this->translateWithKey($permission)]);
            }
        });

        // Save translated permissions
        File::put($translatedPermissionsFilePath, $translatedPermissions->toJson());

        return Command::SUCCESS;
    }

    protected function translateWithKey(string $key)
    {
        $fullName = Str::replace('craftable-pro.', '', $key);
        if (strpos($fullName, '.') === false) {
            return Str::headline(Str::plural($fullName));
        }
        $explodedName = explode('.', $fullName);
        $name = Str::headline($explodedName[0]);

        switch ($explodedName[1]) {
            case 'index':
                return 'List of ' . Str::plural($name);
            case 'create':
                return "Create $name";
            case 'show':
                return "Show $name";
            case 'store':
                return "Store $name";
            case 'edit':
                return "Edit $name";
            case 'update':
                return "Update $name";
            case 'destroy':
                return "Delete $name";
            case "rescan":
                return "Rescan " . Str::plural($name);
            case "publish":
                return "Publish " . Str::plural($name);
            case "export":
                return "Export " . Str::plural($name);
            case "import":
                return "Import " . Str::plural($name);
            case "upload":
                return "Upload " . Str::plural($name);
            case "impersonal-login":
                return "Impersonal login " . $name;
            default:
                return $key;
        }
    }
}
