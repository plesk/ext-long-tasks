<?php

class Modules_LongTasksExample_Task_Fail extends pm_LongTask_Task
{
    const UID = 'fail';
    public $trackProgress = false;

    public function run()
    {
        pm_Log::info('Start method Run for Fail.');
        pm_Log::info('domain name is ' . $this->getParam('domainName', 'none'));
        $duration = (int)$this->getParam('duration', 10);
        $duration = ($duration <= 0) ? 1 : $duration;

        $startTime = time();
        $spend = 0;
        while ($spend <= $duration) {
            $this->updateProgress($spend / $duration * 100);
            sleep(1);
            $spend = (int)(time() - $startTime);
        }

        throw new pm_Exception('ERROR error');
    }

    public function statusMessage()
    {
        pm_Log::info('Start method statusMessage. ID: ' . $this->getId());
        switch ($this->getStatus()) {
            case static::STATUS_QUEUE:
                return pm_Locale::lmsg('taskProgressMessage');
            case static::STATUS_DONE:
                return pm_Locale::lmsg('taskDone', ['id' => $this->getId()]);
            case static::STATUS_ERROR:
                return pm_Locale::lmsg('taskError', ['id' => $this->getId()]);
            case static::STATUS_PING_ERROR:
                return pm_Locale::lmsg('taskPingError', ['id' => $this->getId()]);
            case static::STATUS_CANCELED:
                return pm_Locale::lmsg('taskCancel', ['id' => $this->getId()]);
        }
        return '';
    }

    public function onError()
    {
        pm_Log::info('Start method onError');
        $this->setParam('onError', 1);
    }
}