<TD width="150" bgcolor="#9D9D9D">
	<TABLE width="100%" border="0" cellpadding="6" cellspacing="0">
    <TR>
      <TD>
      	<UL id="navigation">
				{foreach name=menu item=menu from=$nav_menu}
					<LI>
						{* Is menu expandable? *}
						{if true === $menu.expandable}
							{rand var=span_id}
							{rand var=icon_id}
							<LI><H1><A href="javascript:showhideMenu('{$span_id}','{$icon_id}')"><IMG src='/tri_c.gif' id='{$icon_id}' width='14' height='10' border='0'></A><A href="javascript:showhideMenu('{$span_id}','{$icon_id}')">{$menu.desc}</A></H1>
							<SPAN id='{$span_id}' style='display: none'>
						{else}
							{* Display menu description only. *}
							<H1>{$menu.desc|gettext}</H1>
						{/if}
						<UL>
						{foreach item=menuitem from=$menu.menuitem}
							{* Is menu item visible? *}
							{if true === $menuitem.visible}
								{* Display menuitem. *}
								<LI><A href="{$menuitem.link}" title="{$menuitem.desc|gettext}">{$menuitem.desc|gettext}</A></LI>
							{/if}
						{/foreach}
						</UL>
						{if true === $menu.expandable}
							</SPAN>
						{/if}
					</LI>
				{/foreach}
	  		</UL>
			</TD>
    </TR>
	</TABLE>
</TD>
