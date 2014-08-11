jannieforms
===========

Library for easy form generation

 - Define a form once and jannieforms will automatically set up validation clientside and serverside.
 - Can be used as a WordPress plugin

###Todo

 - Add centralized `Callback` class for access both direct and through AJAX.
 - Add clientsize Sanitizer implementation. Sanitize either live or on blur.
 - Add XML form definition parser.
 - Add default styles with horizontal row support.
 - Load forms on demand.
 - Remove low-level configuration of `WRAPPER` and `INPUT` node. (perhaps remove `WRAPPER` node altogether and use `box-sizing: border-box;` for input nodes)
 - Remove `JannieTooltips` from global namespace.
