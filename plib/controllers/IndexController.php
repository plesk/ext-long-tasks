<?php

class IndexController extends pm_Controller_Action
{
    private $taskManager = NULL;

    public function init()
    {
        parent::init();
        pm_Settings::set('readyToStop', false);
        if (is_null($this->taskManager)) {
            $this->taskManager = new pm_LongTask_Manager();
        }
        // Init title for all actions
        $this->view->pageTitle = $this->lmsg('pageTitle', ['product' => 'Plesk']);
    }

    public function indexAction()
    {
        $this->_forward('form');
    }

    public function formAction()
    {
        $domInfo = $this->getDomainInfo();
        $list = $domInfo->webspace->get->result;

        $domainSelector[-1] = 'Global';
        if ($list->status = 'ok') {
            foreach ($list as $domain) {
                if (isset($domain->data->gen_info->name)) {
                    $domainSelector[intval($domain->id)] = strval($domain->data->gen_info->name);
                }
            }
        }
        $form = new Modules_LongTasksExample_Form_CreateForm();
        $form->addElement('select', 'exampleSelect', [
            'label' => 'Select domain',
            'multiOptions' => $domainSelector,
        ]);

        $this->view->tasksbuttons = [
            [
                'title' => 'Run new succeed task',
                'class' => 'sb-app-info',
                'link' => pm_Context::getActionUrl('index', 'add-task') . '/type/succeed',
            ],
            [
                'title' => 'Run new failed task',
                'class' => 'sb-app-info',
                'link' => pm_Context::getActionUrl('index', 'add-task') . '/type/fail',
            ],
            [
                'title' => 'Cancel last not done task',
                'class' => 'sb-app-info',
                'link' => pm_Context::getActionUrl('index', 'cancel-done-task'),
            ],
            [
                'title' => 'Cancel all long tasks',
                'class' => 'sb-app-info',
                'link' => pm_Context::getActionUrl('index', 'cancel-all-task'),
            ],
        ];

        pm_Log::info('Try get succeed tasks');
        $tasks = $this->taskManager->getTasks(['task_succeed']);
        $countDone = 0;
        foreach ($tasks as $task) {
            if ($task->getStatus() == pm_LongTask_Task::STATUS_DONE) {
                $countDone++;
            }
        }
        $form->addElement('SimpleText', 'text', [
            'value' => 'Count of global succeed tasks: ' . count($tasks) . ' Done task: ' . $countDone,
        ]);
        pm_Log::info('Try get failed tasks');
        $tasks = $this->taskManager->getTasks(['task_fail']);
        $countDone = 0;
        foreach ($tasks as $task) {
            if ($task->getStatus() == pm_LongTask_Task::STATUS_ERROR) {
                $countDone++;
            }
        }
        $form->addElement('SimpleText', 'newtext', [
            'value' => 'Count of global failed tasks: ' . count($tasks),
        ]);

        $this->view->form = $form;
    }

    public function addTaskAction()
    {
        $domainId = $this->getParam('domainId', -1);
        $type = $this->getParam('type', 'succeed');
        $domain = $domainId != -1 ? new pm_Domain($domainId) : null;

        pm_Log::info("Create '{$type}' task and set params");
        $task = $type === 'succeed'
            ? new Modules_LongTasksExample_Task_Succeed()
            : new Modules_LongTasksExample_Task_Fail();
        $task->setParams([
            'p1' => 1,
            'p2' => 2,
        ]);
        $task->setParam('p3', 3);

        if (isset($domain)) {
            $task->setParam('domainName', $domain->getName());
        }
        $this->taskManager->start($task, $domain);

        if ($domain) {
            $this->_redirect('/admin/subscription/overview/id/' . $domain->getId(), ['prependBase' => false]);
        } else {
            $this->_redirect('index/form');
        }
    }

    public function cancelDoneTaskAction()
    {
        pm_Log::info('Try get tasks');
        $tasks = $this->taskManager->getTasks(['task_succeed']);
        $i = count($tasks) - 1;
        while ($i >= 0) {
            if ($tasks[$i]->getStatus() != pm_LongTask_Task::STATUS_DONE) {
                $this->taskManager->cancel($tasks[$i]);
                break;
            }
            $i--;
        }
        $this->_redirect('index/form');
    }

    public function cancelAllAction()
    {
        pm_Log::info('Try get tasks');
        $tasks = $this->taskManager->getTasks(['task_succeed']);
        $i = count($tasks) - 1;
        while ($i >= 0) {
            $this->taskManager->cancel($tasks[$i]);
            $i--;
        }
        $this->_redirect('index/form');
    }

    public function cancelAllTaskAction()
    {
        $this->taskManager->cancelAllTasks();
        $this->_redirect('index/form');
    }

    public function getDomainInfo()
    {
        $requestGet = <<<APICALL

        <webspace>
           <get>
            <filter>
            </filter>
             <dataset>
             <gen_info/>
             </dataset>
           </get>
        </webspace>

APICALL;
        $responseGet = pm_ApiRpc::getService()->call($requestGet);
        return $responseGet;
    }
}

