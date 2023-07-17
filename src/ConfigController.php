<?php

namespace Edalzell\Forma;

use Edalzell\Forma\Events\ConfigSaved;
use Illuminate\Http\Request;
use Statamic\Extend\Addon;
use Statamic\Facades\Blueprint as BlueprintAPI;
use Statamic\Facades\Path;
use Statamic\Facades\YAML;
use Statamic\Fields\Blueprint;
use Statamic\Http\Controllers\Controller;
use Stillat\Proteus\Support\Facades\ConfigWriter;

class ConfigController extends Controller
{
    public function edit(Request $request)
    {
        $slug = $request->segment(2);

        $addon = Forma::findBySlug($slug);

        $blueprint = $this->getBlueprint($addon);

        $fields = $blueprint
            ->fields()
            ->addValues($this->preProcess($slug))
            ->preProcess();

        return view('forma::edit', [
            'blueprint' => $blueprint->toPublishArray(),
            'meta' => $fields->meta(),
            'route' => cp_route("{$slug}.config.update", ['handle' => $slug]),
            'title' => $this->cpTitle($addon),
            'values' => $fields->values(),
        ]);
    }

    public function update(Request $request)
    {
        $slug = $request->segment(2);

        $addon = Forma::findBySlug($slug);

        $blueprint = $this->getBlueprint($addon);

        // Get a Fields object, and populate it with the submitted values.
        $fields = $blueprint->fields()->addValues($request->all());

        // Perform validation. Like Laravel's standard validation, if it fails,
        // a 422 response will be sent back with all the validation errors.
        $fields->validate();

        $data = $this->postProcess($fields->process()->values()->toArray());

        $write = ConfigWriter::writeMany($slug, $data);

        ConfigSaved::dispatch($data, $addon);
    }

    private function getBlueprint(Addon $addon): Blueprint
    {
        $path = Path::assemble($addon->directory(), 'resources', 'blueprints', 'config.yaml');

        $yaml = YAML::file($path)->parse();

        if ($yaml['tabs'] ?? false) {
            return BlueprintAPI::make()->setContents($yaml);
        }

        return BlueprintAPI::makeFromFields($yaml);
    }

    protected function postProcess(array $values): array
    {
        return $values;
    }

    protected function preProcess(string $handle): array
    {
        return config($handle);
    }

    public static function cpIcon()
    {
        return 'settings-horizontal';
    }

    public static function cpSection()
    {
        return __('Settings');
    }

    private function cpTitle(Addon $addon)
    {
        return __(':name Settings', ['name' => $addon->name()]);
    }
}
