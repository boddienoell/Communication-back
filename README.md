# Communication-back
Wordpress theme that supports the communication backend via the WP-JSON API V2

This is the most basic theme that is used only when you WP app is used as a REST API service provider.

Supports:
Custom Post types: 
- In the functions.php file, Add custom post types to the var $custom_post_type array.  
This will create the new custom type and include it in the rest service

Custom metadata:
- This includes all metadata in a "_meta" key added to the object.  
