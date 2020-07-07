<?php
namespace App\Traits;
use App\Presenters\Presenter;

trait PresenterBuildable
{
    public function presenter() : Presenter
    {
        $name = (new \ReflectionClass($this))->getShortName();
        $class = sprintf("App\Presenters\%sPresenter", $name);
        return new $class($this);
    }
}