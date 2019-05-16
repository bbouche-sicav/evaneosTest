1. Application analyze (using OOP model, practices used)
2. Search best practices to implement
3. Search code reduction with best practices
4. in TemplateManager::computeText ==> it seems to refactor all the process to replace placeholder for data in the associated Object
Example : $data['user'] ==> call User
          $data['quote'] ==> call Quote
5. Create an interface for all entity to reduce methode TemplateManager::computeText and permit to use the same method named "methods".
=======> Analyze
               ==> all placeholder in tpl has been like [entity_name:property_name]
               ==> all _html must used renderHtml other must use renderText
=======> Goals :    - Template manager must be completely dynamic
                    - Entity properties must be defined at the begining of the class
                    - TPL sent => all placeholder must be used correctsyntax : [entity_name:property_name]
6. Best practices : put all variables namein camelCase
