<?php namespace DMraz\StenoApi\Laravel;

use Illuminate\Support\ServiceProvider;

class StenoApiServiceProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  /**
   * Bootstrap the application events.
   *
   * @return void
   */
  public function boot()
  {
    $this->package('dmraz/steno-api', null, __DIR__);

    if($this->app['config']->get('steno-api::mock_server.enabled'))
    {
      $server = new MockServer();
      $server->loadConfig();
      $server->start();
    }

    if($this->app['config']->get('steno-api::document_server.enabled'))
    {
      $server = new DocumentServer();
      $server->loadConfig();
      $server->start();
    }
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {

  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array();
  }

}
