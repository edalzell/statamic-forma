<?php

namespace Edalzell\Forma;

use Edalzell\Forma\Events\ConfigSaved;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Statamic\Facades\Blueprint as BlueprintAPI;
use Statamic\Facades\Path;
use Statamic\Facades\YAML;
use Statamic\Fields\Blueprint;
use Statamic\Http\Controllers\Controller;
use Stillat\Proteus\Support\Facades\ConfigWriter;

class ConfigController extends Controller
{
    public function edit(Request $request): View|Factory
    {
        $slug = $request->segment(2);

        $blueprint = $this->getBlueprint($addon = Forma::findBySlug($slug));

        $fields = $blueprint
            ->fields()
            ->addValues($this->preProcess($addon->statamicAddon()->slug()))
            ->preProcess();

        return view('forma::edit', [
            'blueprint' => $blueprint->toPublishArray(),
            'meta' => $fields->meta(),
            'route' => cp_route("{$slug}.config.update", ['handle' => $slug]),
            'title' => $this->cpTitle($addon),
            'values' => $fields->values(),
        ]);
    }

    public function update(Request $request): void
    {
        $slug = $request->segment(2);

        $blueprint = $this->getBlueprint($addon = Forma::findBySlug($slug));

        // Get a Fields object, and populate it with the submitted values.
        $fields = $blueprint->fields()->addValues($request->all());

        // Perform validation. Like Laravel's standard validation, if it fails,
        // a 422 response will be sent back with all the validation errors.
        $fields->validate();

        $data = $this->postProcess($fields->process()->values()->toArray());

        ConfigWriter::writeMany($addon->configHandle(), $data);

        ConfigSaved::dispatch($data, $addon->statamicAddon());
    }

    private function getBlueprint(FormaAddon $addon): Blueprint
    {
        $path = Path::assemble($addon->statamicAddon()->directory(), 'resources', 'blueprints', 'config.yaml');

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
        return config(Forma::findBySlug($handle)->configHandle());
    }

    public static function cpIcon(): string
    {
        return 'settings-horizontal';
    }

    public static function cpSection(): string
    {
        return __('Settings');
    }

    private function cpTitle(FormaAddon $addon): string
    {
        return __(':name Settings', ['name' => $addon->statamicAddon()->name()]);
    }
}
