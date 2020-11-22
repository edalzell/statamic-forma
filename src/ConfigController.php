<?php

namespace Edalzell\Forma;

use Illuminate\Http\Request;
use Statamic\Facades\Blueprint as BlueprintAPI;
use Statamic\Facades\Path;
use Statamic\Facades\YAML;
use Statamic\Fields\Blueprint;
use Statamic\Http\Controllers\Controller;
use Stillat\Proteus\Support\Facades\ConfigWriter;

class ConfigController extends Controller
{
    public function edit(string $handle)
    {
        $blueprint = $this->getBlueprint($handle);

        $fields = $blueprint
            ->fields()
            ->addValues($this->preProcess($handle))
            ->preProcess();

        return view('forma::edit', [
            'blueprint' => $blueprint->toPublishArray(),
            'meta' => $fields->meta(),
            'route' => cp_route("{$handle}.config.update", ['handle' => $handle]),
            'values' => $fields->values(),
        ]);
    }

    public function update(string $handle, Request $request)
    {
        $blueprint = $this->getBlueprint($handle);

        // Get a Fields object, and populate it with the submitted values.
        $fields = $blueprint->fields()->addValues($request->all());

        // Perform validation. Like Laravel's standard validation, if it fails,
        // a 422 response will be sent back with all the validation errors.
        $fields->validate();

        ConfigWriter::writeMany($handle, $this->postProcess($fields->process()->values()->toArray()));
    }

    private function getBlueprint(string $handle): Blueprint
    {
        $addon = Forma::findByHandle($handle);

        $path = Path::assemble($addon->directory(), 'resources', 'blueprints', 'config.yaml');

        return BlueprintAPI::makeFromFields(YAML::file($path)->parse());
    }

    protected function postProcess(array $values): array
    {
        return $values;
    }

    protected function preProcess(string $handle): array
    {
        return config($handle);
    }
}
