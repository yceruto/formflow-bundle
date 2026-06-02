CHANGELOG
=========

0.4.1
-----
 * Add `form_flow_*` Twig helper functions to access values from `FormFlowCursor`
 * Add `form_flow_*` Twig helper functions to work with nested steps

0.4.0
-----
 * [Breaking Changes] Renamed the Form Flow classes to follow Symfony's native naming convention (`<Thing>Flow` instead of `Flow<Thing>`). The `Yceruto\FormFlowBundle\Form\Flow` namespace is unchanged; only class names changed:

   | Before                     | After                            |
   |----------------------------|----------------------------------|
   | `AbstractFlowButtonType`   | `AbstractButtonFlowType`         |
   | `FlowButton`               | `ButtonFlow`                     |
   | `FlowButtonBuilder`        | `ButtonFlowBuilder`              |
   | `FlowButtonInterface`      | `ButtonFlowInterface`            |
   | `FlowButtonTypeInterface`  | `ButtonFlowTypeInterface`        |
   | `FlowCursor`               | `FormFlowCursor`                 |
   | `FlowStepNode`             | `StepFlowNode`                   |
   | `FlowStepBuilder`          | `StepFlowBuilder`                |
   | `FlowStepBuilderInterface` | `StepFlowBuilderConfigInterface` |
   | `FlowStepConfigInterface`  | `StepFlowConfigInterface`        |
   | `Type\FlowButtonType`      | `Type\ButtonFlowType`            |
   | `Type\FlowFinishType`      | `Type\FinishFlowType`            |
   | `Type\FlowNavigatorType`   | `Type\NavigatorFlowType`         |
   | `Type\FlowNextType`        | `Type\NextFlowType`              |
   | `Type\FlowPreviousType`    | `Type\PreviousFlowType`          |
   | `Type\FlowResetType`       | `Type\ResetFlowType`             |

 * [Breaking Changes] As a consequence of the type renames, the form type block prefixes changed: `flow_button` → `button_flow`, `flow_finish` → `finish_flow`, `flow_navigator` → `navigator_flow`, `flow_next` → `next_flow`, `flow_previous` → `previous_flow`, `flow_reset` → `reset_flow`. Update any custom form themes that reference the old block names.

0.3.1
-----
 * Add support for Symfony 8.0

0.3.0
-----
 * Add support for grouping and nested steps in `FormFlowType`
 * Add `FlowStepNode` for representing the step flow tree (forest graph)
 * Add `createStepGroup()` method to `FormFlowBuilderInterface`
 * Add `setGroup()`, `addStep()`, `removeStep()` methods to `FlowStepBuilderInterface`
 * Add `isGroup()`, `getSteps()`, `hasStep()`, `getStep()` methods to `FlowStepConfigInterface`
 * Add `with_reset` option to `FlowNavigatorType` to conditionally include the reset button (defaults to `false`)
 * Add `getStepIndexOf()`, `getParentStep()`, `getChildSteps()`, `getCurrentStepNode()`, `getStepNode()` and `getRootStepNodes()` methods to `FlowCursor`
 * `FlowCursor` now accepts either a flat list of step names or a list of `FlowStepConfigInterface` instances, and flattens nested trees via DFS pre-order traversal
 * Add new view variables to `FormFlowType`: `level`, `is_before_current_step`, `has_current_step_descendant`, `is_after_current_step`, `is_group`, `children`, `visible_children`
 * [Breaking Changes] `FlowNavigatorType` no longer adds a `reset` button by default; pass `'with_reset' => true` to opt in
 * [Breaking Changes] `FlowStepBuilder::setGroup()`, `addStep()`, `removeStep()` return type changed from `FlowStepBuilderInterface` to `static`

0.2.3
-----
 * Add `buildViewFlow()` and `finishViewFlow` methods to `FormFlowTypeInterface`

0.2.2
-----
 * Add `AbstractFlowButtonType` to simplify button type creation

0.2.1
-----
 * Add `buildFormFlow(FormFlowBuilderInterface $builder, array $options)` method to `FormFlowTypeInterface`

0.2.0
-----
 * [Breaking Changes] Renaming classes (see https://github.com/yceruto/formflow-bundle/pull/4)

0.1.3
-----
 * [Breaking Changes] Refactor action handler execution

   *Before*
   ```php
   $flow->handleAction();
   ```

   *After*
   ```php
   $flow->getClickedActionButton()->handle();
   ```

0.1.2
-----
 * Fixed profile collector

0.1.1
-----
 * Add more tests 

0.1.0
-----
 * Initial Proposal 
