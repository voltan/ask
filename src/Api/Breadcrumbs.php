<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Ask\Api;

use Pi;
use Pi\Application\Api\AbstractBreadcrumbs;

class Breadcrumbs extends AbstractBreadcrumbs
{
    /**
     * {@inheritDoc}
     */
    public function load()
    {
        // Get params
        $params = Pi::service('url')->getRouteMatch()->getParams();
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Check breadcrumbs
        if ($config['view_breadcrumbs']) {
            // Set module link
            $moduleData = Pi::registry('module')->read($this->getModule());
            // Set module url
            $moduleData['moduleUrl'] = Pi::url(Pi::service('url')->assemble('ask', array(
                'module' => $this->getModule(),
            )));
            // Set result
            $result = array();
            // Make tree
            switch ($params['controller']) {
                case 'index':
                    $result[] = array(
                        'label' => $moduleData['title'],
                    );
                    break;

                case 'project':
                    $project = Pi::api('project', 'ask')->getProject($params['slug'], 'slug');
                    $result[] = array(
                        'href'  => $moduleData['moduleUrl'],
                        'label' => $moduleData['title'],
                    );
                    $result[] = array(
                        'label' => $project['title'],
                    );
                    break;

                case 'tag':
                    $result[] = array(
                        'href'  => $moduleData['moduleUrl'],
                        'label' => $moduleData['title'],
                    );

                    if (!empty($params['slug'])) {
                        $result[] = array(
                            'label' => __('Tag list'),
                            'href'  => Pi::url(Pi::service('url')->assemble('ask', array(
                                'controller' => 'tag',
                                'action'     => 'list',
                            ))),
                        );
                        $result[] = array(
                            'label' => $params['slug'],
                        );
                    } else {
                        $result[] = array(
                            'label' => __('Tag list'),
                        );
                    }
                    break;

                case 'question':
                    $question = Pi::api('question', 'ask')->getQuestion($params['slug'], 'slug');
                    $result[] = array(
                        'href'  => $moduleData['moduleUrl'],
                        'label' => $moduleData['title'],
                    );
                    if ($question['project_id'] > 0) {
                        $project = Pi::api('project', 'ask')->getProject($question['project_id']);
                        $result[] = array(
                            'href'  => $project['projectUrl'],
                            'label' => $project['title'],
                        );
                    }
                    $result[] = array(
                        'label' => $question['title'],
                    );
                    break;

                case 'answer':
                    $question = Pi::api('question', 'ask')->getQuestion($params['slug'], 'slug');
                    $result[] = array(
                        'href'  => $moduleData['moduleUrl'],
                        'label' => $moduleData['title'],
                    );
                    if ($question['project_id'] > 0) {
                        $project = Pi::api('project', 'ask')->getProject($question['project_id']);
                        $result[] = array(
                            'href'  => $project['projectUrl'],
                            'label' => $project['title'],
                        );
                    }
                    $result[] = array(
                        'href'  => $question['questionUrl'],
                        'label' => mb_substr(strip_tags($question['title']), 0, 50, 'utf-8' ) . "...",
                    );
                    $result[] = array(
                        'label' => __('Answer to question'),
                    );
                    break;

                case 'submit':
                    $result[] = array(
                        'href'  => $moduleData['moduleUrl'],
                        'label' => $moduleData['title'],
                    );
                    if (!empty($params['slug'])) {
                        $project = Pi::api('project', 'ask')->getProject($params['slug'], 'slug');
                        $result[] = array(
                            'href'  => $project['projectUrl'],
                            'label' => $project['title'],
                        );
                    }
                    $result[] = array(
                        'label' => __('Ask a new Question'),
                    );
                    break;
            }
            return $result;
        }
    }
}