<TD width="150" class="pgnavbar">
	<TABLE width="100%" border="0" cellpadding="6" cellspacing="0">
    <TR>
      <TD>
      	<UL id="navigation">
				{foreach name=menu item=menu from=$menu_items}
					{* Is menu expandable? *}
					{if true === $menu.expandable}
						{rand var=span_id} {* Create unique id. *}
						{rand var=icon_id} {* Create unique id. *}
						{if true === $menu.is_expanded}
							<li><h1><a href="javascript:showhideMenu('{$span_id}','{$icon_id}')"><img src='/tri_o.gif' id='{$icon_id}' width='14' height='10' border='0'></a><a href="javascript:showhideMenu('{$span_id}','{$icon_id}')">{$menu.desc|gettext}</a></h1>
							<span id='{$span_id}'>
						{else}
							<li><h1><a href="javascript:showhideMenu('{$span_id}','{$icon_id}')"><img src='/tri_c.gif' id='{$icon_id}' width='14' height='10' border='0'></a><a href="javascript:showhideMenu('{$span_id}','{$icon_id}')">{$menu.desc|gettext}</a></h1>
							<span id='{$span_id}' style='display: none'>
						{/if}
					{else}
						{* Display menu description only. *}
						<li><h1>{$menu.desc|gettext}</h1>
					{/if}
					{* Open new navigation layer. *}
					<ul>
					{* Display menu items. *}
					{foreach item=menuitem from=$menu.menuitem}
						{* Is menu item visible? *}
						{if true === $menuitem.visible}
							{* Display menuitem. *}
							<LI><A href="{$menuitem.link}" title="{$menuitem.desc|gettext}">{$menuitem.desc|gettext}</A></LI>
						{/if}
					{/foreach}
					{* Close navigation layer. *}
					</ul></li>
					{* Is menu expandable? *}
					{if true === $menu.expandable}
						</SPAN>
					{/if}
				{/foreach}
	  		</UL>
			</TD>
    </TR>
	</TABLE>
</TD>
