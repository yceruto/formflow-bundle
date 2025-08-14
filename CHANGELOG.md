CHANGELOG
=========

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
