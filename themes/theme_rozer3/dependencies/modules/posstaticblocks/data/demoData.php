<?php

class demoData
{
    public function initData()
    {
        $return = true;
        $languages = Language::getLanguages(true);
        $id_shop = Context::getContext()->shop->id;
        $id_hook_nav = (int)Hook::getIdByName('displayNav');
        $id_hook_home = (int)Hook::getIdByName('displayHome');
        $id_hook_bottom = (int)Hook::getIdByName('displayContainerbottom');
        
        $queries = [
            'INSERT INTO `'._DB_PREFIX_.'pos_staticblock` (`id_pos_staticblock`, `id_hook`, `position`, `name`,`active`) VALUES
                (1, '.$id_hook_nav.', 1, "static nav", 1),
                (2, '.$id_hook_home.', 2, "Static Cms", 1),
                (3, '.$id_hook_home.', 3, "Home Banner", 1),
                (4, '.$id_hook_bottom.', 4, "Home Banner2", 1)'
        ];

        foreach (Language::getLanguages(true, Context::getContext()->shop->id) as $lang) {
            $queries[] = 'INSERT INTO `'._DB_PREFIX_.'pos_staticblock_lang` (`id_pos_staticblock`, `id_lang`, `content`) VALUES
                (1, '.(int)$lang['id_lang'].', \'<div id="_desktop_static">
				<div class="static-nav">Welcome you to Rozer Store!</div>
				</div>\'),
                (2, '.(int)$lang['id_lang'].', \'<div class="static_cms">
				<div class="row">
				<div class="col-cms col-xs-12 col-sm-6 col-md-6 col-lg-3">
				<div class="box_cms"><img src="/pos_rozer/img/cms/icon_cms1.png" alt="" class="img-responsive" />
				<div class="txt_cms">
				<h2>Free Shipping</h2>
				<p>On all orders over $75.00</p>
				</div>
				</div>
				</div>
				<div class="col-cms col-xs-12 col-sm-6 col-md-6 col-lg-3">
				<div class="box_cms"><img src="/pos_rozer/img/cms/icon_cms2.png" alt="" class="img-responsive" />
				<div class="txt_cms">
				<h2>Free Returns</h2>
				<p>Returns are free within 9 days</p>
				</div>
				</div>
				</div>
				<div class="col-cms col-xs-12 col-sm-6 col-md-6 col-lg-3">
				<div class="box_cms"><img src="/pos_rozer/img/cms/icon_cms3.png" alt="" class="img-responsive" />
				<div class="txt_cms">
				<h2>Support 24/7</h2>
				<p>Contact us 24 hours a day</p>
				</div>
				</div>
				</div>
				<div class="col-cms col-xs-12 col-sm-6 col-md-6 col-lg-3">
				<div class="box_cms"><img src="/pos_rozer/img/cms/icon_cms4.png" alt="" class="img-responsive" />
				<div class="txt_cms">
				<h2>100% Payment Secure</h2>
				<p>Your payment are safe with us.</p>
				</div>
				</div>
				</div>
				</div>
				</div>\'),
				(3, '.(int)$lang['id_lang'].', \'<div class="home-banner">
				<div class="row">
				<div class="col col-md-4 col-xs-12">
				<div class="banner-box"><a href="#"><img src="/pos_rozer/img/cms/1_1.jpg" alt="" /></a></div>
				</div>
				<div class="col col-md-4 col-xs-12">
				<div class="banner-box"><a href="#"><img src="/pos_rozer/img/cms/2_1.jpg" alt="" /></a></div>
				</div>
				<div class="col col-md-4 col-xs-12">
				<div class="banner-box"><a href="#"><img src="/pos_rozer/img/cms/3_1.jpg" alt="" /></a></div>
				</div>
				</div>
				</div>\'),
				(4, '.(int)$lang['id_lang'].', \'<div class="home-banner">
				<div class="row">
				<div class="col col-md-6 col-xs-12">
				<div class="banner-box"><a href="#"><img src="/pos_rozer/img/cms/4_1.jpg" alt="" /></a></div>
				</div>
				<div class="col col-md-6 col-xs-12">
				<div class="banner-box"><a href="#"><img src="/pos_rozer/img/cms/5_1.jpg" alt="" /></a></div>
				</div>
				</div>
				</div>\')'
            ;
        }

        $queries[] = 'INSERT INTO `'._DB_PREFIX_.'pos_staticblock_shop` (`id_pos_staticblock`, `id_shop`) VALUES
                (1, 1),
                (2, 1),
                (3, 1),
                (4, 1)';   

        foreach ($queries as $query) {
            $return &= Db::getInstance()->execute($query);
        }

        return $return;
    }
}
?>