<div class="wrap">
        <table style="width:100%;">
            <tbody>
            <tr>
                <td colspan="2">
                    <div id="icon-users" class="icon32"><br /></div>
                    <h2>Network Posts Extended Help</h2>
                    <hr />
                </td>
            </tr>
            <tr>
                <td>
                    <form method="post" action="options.php">
                        <?php wp_nonce_field('update-options'); ?>
                        <?php //$styling  = get_option('net-style'); ?>
                        <?php $pages = get_option('hide_readmore_link_pages');?>
                        <!--Add extra css styling: <?php //echo "Here is a good source for custom css styling: <a target='ejejcsingle' href='http://www.w3schools.com/css/css_id_class.asp'>w3schools class tutorial</a>"; ?></br>
        <textarea style="width: 500px; height: 500px;" name="net-style" ><?php //echo $styling; ?></textarea>-->
                        <div>
                            <input type="checkbox" name="use_wpml" id="use_wpml" value="1" <?php checked('1',get_option('use_wpml')); ?>/>
                            <label for="use_wpml">Use WPML</label>
                        </div>
                        <br/><br/>
                        <div>
                            <input type="checkbox" name="hide_all_readmore_links" id="hide_all_readmore_links" value="1" <?php checked('1', get_option('hide_all_readmore_links'));?>/>
                            <label for="hide_all_readmore_links">Hide all 'read more' links</label>
                        </div>
                        <br/><br/>
                        Pages without "read more" links (Write titles of pages. Each title must ends with ";" symbol):<br/>
                        <textarea style="width: 500px; height: 500px;" name="hide_readmore_link_pages"><?php echo $pages; ?></textarea>
                        </br>
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="page_options" value="use_wpml, hide_readmore_link_pages, hide_all_readmore_links" />
                        <input type="submit" value="Save Changes" />
                    </form>
                </td>
                <td style="vertical-align:top;margin-left:100px;">
                    If you like this plugin please donate:
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                        <input type="hidden" name="cmd" value="_donations">
                        <input type="hidden" name="business" value="john@johncardell.com">
                        <input type="hidden" name="lc" value="US">
                        <input type="hidden" name="item_name" value="Network Shared Posts">
                        <input type="hidden" name="no_note" value="0">
                        <input type="hidden" name="currency_code" value="USD">
                        <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                    </form>

                    </br></br>

                    <?php
                    echo "For a complete tutorial visit:<br /><a target='ejecsingle' href='https://wp-plugins.johncardell.com/network-posts-extended/'>https://wp-plugins.johncardell.com/network-posts-extended/</a><br /><br />";
                    echo "Need professional help for your blog, plugin, or script? Try Freelancer:<br /><a target='ejejcsingle' href='https://www.freelancer.com/affiliates/johnzenavw' title='Higher a Freelancer at Freelancer.com'><img alt='Freelance Jobs at Freelancer.com' src='/wp-content/plugins/network-posts-extended/pictures/Freelancer-black.jpg' style='width:480px;height:auto;' class='img-hover' /></a><br />";
                    echo "For quality web hosting use <a href='https://interserver.net/dock/website-289738.html' title='Supreme Web Hosting'>Interserver.net</a>:<br /><a target='ejejcsingle' href='https://interserver.net/dock/website-289738.html' title='Quality Affordable Web Hosting'><img alt='Interserver.net quality web hosting' src='/wp-content/plugins/network-posts-extended/pictures/interserverwebhosting.gif' style='width:480px;height:auto;' class='img-hover' /></a><br />";
					echo "<a href='http://amzn.to/1Yxr5an' title='Amazon Books on WordPress and HTML (CSS)'>Recommended Books For Learning WordPress and HTML (CSS) <img src='/wp-content/plugins/network-posts-extended/pictures/wordpressfordummies.jpg' alt='Amazon Books on WordPress and HTML (CSS)' style='vertical-align:middle;width:auto;height:59px;'/></a><br />";
                    ?>
                    </br></br>
                </td>
            </tr>
            </tbody>
        </table>
    </div>