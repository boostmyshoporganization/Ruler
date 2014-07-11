<?php

/*
 * This file is part of the Ruler package, an OpenSky project.
 *
 * (c) 2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ruler;

/**
 * Rule class.
 *
 * A Rule is a conditional Proposition with an (optional) action which is
 * executed upon successful evaluation.
 *
 * @author Justin Hileman <justin@justinhileman.info>
 */
class Rule implements Proposition
{
    protected $condition;
    protected $action;

    /**
     * Rule constructor.
     *
     * @param Proposition $condition Propositional condition for this Rule
     * @param callback $action Action (callable) to take upon successful Rule execution (default: null)
     */
    public function __construct(Proposition $condition, $actionTrue = null, $actionFalse = null)
    {
        $this->condition = $condition;
        $this->actionTrue = $actionTrue;
        $this->actionFalse = $actionFalse;
    }

    /**
     * Evaluate the Rule with the given Context.
     *
     * @param Context $context Context with which to evaluate this Rule
     *
     * @return boolean
     */
    public function evaluate(Context $context)
    {
        return $this->condition->evaluate($context);
    }

    /**
     * Execute the Rule with the given Context.
     *
     * The Rule will be evaluated, and if successful, will execute its
     * $action callback.
     *
     * @param  Context $context Context with which to execute this Rule
     * @throws \LogicException
     */
    public function execute(Context $context)
    {
        if ($this->evaluate($context) && isset($this->actionTrue)) {
            if (!is_callable($this->actionTrue)) {
                throw new \LogicException('Rule actions must be callable.');
            }

            return call_user_func($this->actionTrue, $context->values());
        }

        if ($this->actionFalse !== null) {
            return call_user_func($this->actionFalse, $context->values());
        }
    }
}
