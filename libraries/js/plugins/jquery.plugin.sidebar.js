/*
 * SIDEBAR MENU
 * ------------
 * This is a custom plugin for the sidebar menu. It provides a tree view.
 * 
 * Usage:
 * $(".sidebar).tree();
 * 
 * Note: This plugin does not accept any options. Instead, it only requires a class
 *       added to the element that contains a sub-menu.
 *       
 * When used with the sidebar, for example, it would look something like this:
 * <ul class='sidebar-menu'>
 *      <li class="treeview active">
 *          <a href="#>Menu</a>
 *          <ul class='treeview-menu'>
 *              <li class='active'><a href=#>Level 1</a></li>
 *          </ul>
 *      </li>
 * </ul>
 * 
 * Add .active class to <li> elements if you want the menu to be open automatically
 * on page load. See above for an example.
 */
(function($) {
    "use strict";

    $.fn.tree = function() {

        return this.each(function() {
            var btn 		= $(this).children("a").first();
            var menu 		= $(this).children(".treeview-menu").first();

			var liActive = $(this).find('li.active');
            if(liActive.length > 0){
            	liActive.parents(".treeview").addClass("active");
                menu.show();
                btn.find(".fa-plus").removeClass("fa-plus").addClass("fa-minus");
                btn.find(".fa-folder").removeClass("fa-folder").addClass("fa-folder-open");
			}

            //initialize already active menus
            var isActive = $(this).hasClass('active');
            if(isActive) {
            }

			//Slide open or close the menu on link click
            btn.click(function(e) {
                e.preventDefault();
                if (isActive) {
                    //Slide up to close menu
                    menu.slideUp();
                    isActive = false;
                    btn.find(".fa-minus").removeClass("fa-minus").addClass("fa-plus");
                	btn.find(".fa-folder-open").removeClass("fa-folder-open").addClass("fa-folder");
                    btn.parent("li").removeClass("active");
                } else {
                    //Slide down to open menu
                    menu.slideDown();
                    isActive = true;
                    btn.find(".fa-plus").removeClass("fa-plus").addClass("fa-minus");
                	btn.find(".fa-folder").removeClass("fa-folder").addClass("fa-folder-open");
                    btn.parent("li").addClass("active");
                }
            });

            /* Add margins to submenu elements to give it a tree look */
			menu.find("li > a").each(function(i, v){ 
				if($(this).parents(".treeview-menu").length == 1){
					$(this).css({"margin-left": "10px"});
				} else if($(this).parents(".treeview-menu").length == 2){
					$(this).css({"margin-left": "20px"});
				} else if($(this).parents(".treeview-menu").length == 3){
					$(this).css({"margin-left": "40px"});
				}
            });

        });

    };


}(jQuery));

