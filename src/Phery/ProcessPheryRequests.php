<?php

namespace Laravel\Phery;

use Illuminate\Http\Request;
use Log;
use Notification;
use Session;

trait ProcessPheryRequests
{

    /**
     * PheryResponse object
     * @var unknown
     */
    public $pheryResponse;

    /**
     * Checks to see if the request is
     */
    public function isPhery()
    {
        return Phery::is_ajax();
    }

    /**
     * Initialize Phery
     * @param Request $request
     */
    public function initializePhery()
    {
        // Log::debug("Inside initializePhery()...");
        $action = request()->input('phery')['remote'];
        $method = 'ajax'.ucfirst($action);
        $class = '\\'.get_class($this);
        $this->pheryResponse = PheryResponse::factory();
        // Create the phery instance
        try {
            $config = config('phery');
            Phery::instance($config)->callback([
                'after' => [[__CLASS__, 'cleanupPhery']]
            ])
            ->set([$action => [$this, $method]])
            ->process();
            return response($this->pheryResponse->render())->header('Content-Type', 'application/json');
        } catch (PheryException $exc) {
            Log::error($exc->getMessage());
        }
        return false;
    }

    /**
     * Cleanup function that gets called after every ajax executed by phery
     * @param unknown $ajaxData
     * @param unknown $callbackSpecificData
     * @param unknown $answer
     * @param unknown $pheryInstance
     */
    public static function cleanupPhery($ajaxData, $callbackSpecificData, $pheryResponse, $pheryInstance)
    {
        Log::debug("...cleanupPhery()");
        /*
        $messages = Notification::container()->all();
        if (!empty($messages)) {
            foreach ($messages as $message) {
                $script = "$.notify({"
                        //. "    title: '<strong>Heads up!</strong>', "
                        . "    message: '".$message->getMessage()."' "
                        . "}, { "
                        . "    type: '".$message->getType()."'"
                        . "});";
                $pheryResponse->script($script);
            }
        }
        */
        // Also output the stuff from Flash
        if (Session::has('flash_notification')) {
            foreach (Session::get('flash_notification') as $alert) {
                $message = "";
                if (!empty($alert['title'])) {
                    $message .= "<b>".$alert['title']."</b>: ";
                }
                $message = $alert['message'];
                // $alert['title']
                $script = "$.notify({"
                        . "    message: '".$message."' "
                        . "}, { "
                        . "    type: '".$alert['level']."'"
                        . "});";
                $pheryResponse->script($script);
            }
        }
        // Return
        return;
    }

    /**
     * Load a div with a URL
     */
    protected function loadPheryDiv($div, $url)
    {
        $this->pheryResponse->script("$('#".$div."').load('".$url."');");
    }

    /**
     * Load a tab with a URL
     */
    protected function loadPheryTab($div, $index)
    {
        $this->pheryResponse->script("$('#".$div."').tabs('load',".$index.")");
    }

    /**
     * Load a tab with a URL
     * @todo
     */
    protected function loadPheryTabWithCookie($div, $cookie)
    {
        /*
        $script = "var tabIndex = $.cookie('" . $cookie . "'); ";
        $script .= "tabIndex = parseInt(tabIndex); ";
        $script .= "$('#" . $div . "').tabs('load', tabIndex);";
        $this->pheryResponse->script($script);
        */
    }


}
