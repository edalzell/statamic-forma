<?php

namespace Edalzell\Forma;

use Edalzell\Forma\Events\ConfigSaved;
use Illuminate\Http\Request;
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
            ->addValues($this->preProcess($addon->statamicAddon()->slug()))
            ->preProcess();

        return view('forma::edit', [
            'blueprint' => $blueprint->toPublishArray(),
            'meta' => $fields->meta(),
            'route' => cp_route("{$slug}.config.update", ['handle' => $slug]),
            'values' => $fields->values(),
        ]);
    }

    public function update(Request $request)
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

        ConfigSaved::dispatch($data);
    }

    private function getBlueprint(FormaAddon $addon): Blueprint
    {
        $path = Path::assemble($addon->statamicAddon()->directory(), 'resources', 'blueprints', 'config.yaml');

        return BlueprintAPI::makeFromFields(YAML::file($path)->parse());
    }

    protected function postProcess(array $values): array
    {
        return $values;
    }

    protected function preProcess(string $handle): array
    {
        return config(Forma::findBySlug($handle)->configHandle());
    }
}
