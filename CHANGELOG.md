CHANGELOG
=========

0.3.0
-----
 * Bump minimum PHP version to 8.4
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
