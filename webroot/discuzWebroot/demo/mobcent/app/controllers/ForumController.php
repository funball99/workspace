<?php

class ForumController extends MobcentController 
{
    protected function beforeAction($action)
    {
        parent::beforeAction($action);

        if ($action->id != 'topiclist')
            $this->checkUserAccess();

        return true;
    }

    public function actions()
    {
        return array(
            'topiclist' => 'application.controllers.forum.TopicListAction',
            'postlist' => 'application.controllers.forum.PostListAction',
        );
    }
}