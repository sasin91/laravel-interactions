<?php

namespace Sasin91\LaravelInteractions\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class InteractionMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:interaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Interaction';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Interaction';

    /**
     * @var string
     */
    protected $contract;

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $name = $this->parseName($this->getNameInput());
        $this->contract($name);

        $path = $this->getPath($name);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ($this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        if ($this->option('contract')) {
            $this->files->put($this->getPath($this->contract), $this->buildContract($name));
        }

        $this->files->put($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
    }

    /**
     * Set the Contract variable.
     *
     * @param $name
     */
    protected function contract($name)
    {
        $this->contract = $name.'Contract';
    }

    protected function buildContract($name)
    {
        $stub = $this->files->get($this->getContractStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name.'Contract');
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base repository import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('contract')) {

            $contractClass = $this->contract;

            $replace = [
                'DummyFullContractClass'    =>  $contractClass,
                'DummyContractClass'        =>  class_basename($contractClass)
            ];
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    protected function getContractStub()
    {
        return $this->stubPath('contract.stub');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('contract')) {
            return $this->stubPath('interaction.contract.stub');
        }

        return $this->stubPath('interaction.stub');
    }

    protected function stubPath($stub)
    {
        return __DIR__.'/../../../stubs/'.$stub;
    }
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Interactions';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['contract', 'c', InputOption::VALUE_NONE, 'Generate Contract for the Interaction.']
        ];
    }
}
