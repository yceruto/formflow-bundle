CHANGELOG
=========

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
