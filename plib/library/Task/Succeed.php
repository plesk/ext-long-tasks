<?php

class Modules_LongTasksExample_Task_Succeed extends pm_LongTask_Task
{
    const UID = 'succeed';
    public $trackProgress = true;
    private static $progressText = 'Progress is ';

    public function run()
    {
        pm_Log::info('Start method Run for Succeed.');
        pm_Log::info('p2 is '.$this->getParam('p2'));
        pm_Log::info('p3 is '.$this->getParam('p3'));
        $this->updateProgress(0);
        pm_Log::info(self::$progressText.$this->getProgress());
        sleep(3);
        $this->updateProgress(20);
        pm_Log::info(self::$progressText.$this->getProgress());
        sleep(3);
        $this->updateProgress(40);
        pm_Log::info(self::$progressText.$this->getProgress());
        sleep(3);
        $this->updateProgress(60);
        pm_Log::info(self::$progressText.$this->getProgress());
        pm_Log::info('Status after 60% progress: ' . $this->getStatus());
        sleep(3);
    }

    public function statusMessage()
    {
        pm_Log::info('Start method statusMessage. ID: ' . $this->getId().' with status: '.$this->getStatus());
        switch ($this->getStatus()) {
            case static::STATUS_QUEUE:
                return pm_Locale::lmsg('taskProgressMessage');
            case static::STATUS_DONE:
                return pm_Context::getPlibDir();
            case static::STATUS_ERROR:
                return pm_Locale::lmsg('taskError', ['id' => $this->getId()]);
            case static::STATUS_NOT_STARTED:
                return pm_Locale::lmsg('taskPingError', ['id' => $this->getId()]);
        }
        return '';
    }

    public function onStart()
    {
        pm_Log::info('Start method onStart');
        pm_Log::info('p1 is '.$this->getParam('p1'));
        $this->setParam('onStart', 1);
    }

    public function onDone()
    {
        pm_Log::info('Start method onDone');
        $this->setParam('onDone', 1);
        pm_Log::info('End method onDone');
        pm_Log::info('Status: ' . $this->getStatus());
    }
}