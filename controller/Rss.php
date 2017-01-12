<?php
defined('COT_CODE') or die('Wrong URL.');


/**
 * Ads board module for Cotonti Siena
 *     advboard_controller_Rss
 *     Rss Controller class
 *
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
class advboard_controller_Rss
{
    public function indexAction() {
        global $structure;

        if(!cot_module_active('rss')) cot_die_message(404, TRUE);

        $c = cot_import('c', 'G', 'TXT');

        if(!empty($c)) {
            if(!isset($structure['advboard'][$c])) cot_die_message(404, TRUE);

            list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('advboard', $c);
            cot_block(cot::$usr['auth_read']);
        }

        $rss_title = cot::$L['advboard_rss_feed'].cot::$cfg['maintitle'];
        $rss_link = cot::$cfg['mainurl'];
        $rss_description = cot::$cfg['subtitle'];
        $domain = cot::$sys['domain'];

        $condition = array(
            array('state', advboard_model_Advert::PUBLISHED),
            array('begin', cot::$sys['now'], '<='),
            array('SQL', "expire = 0 OR expire > ".cot::$sys['now']),
        );

        if(!empty($c)) {
            $rss_title = cot::$L['advboard_rss_feed'].$structure['advboard'][$c]['title'].' - '.cot::$cfg['maintitle'];
            $condition[] = array('category', $c);
        }

        $advertisement = advboard_model_Advert::findByCondition($condition, cot::$cfg['rss']['rss_maxitems'], 0, array(array('sort', 'desc')));

        $t = new XTemplate(cot_tplfile('rss'));

        $now = cot::$sys['now'];
        $now += cot::$usr['timezone'] * 3600;

        $t->assign(array(
            'RSS_ENCODING' => cot::$cfg['rss']['rss_charset'],
            'RSS_TITLE' => htmlspecialchars($rss_title),
            'RSS_LINK' => $rss_link,
            'RSS_LANG' => cot::$cfg['defaultlang'],
            'RSS_DESCRIPTION' => htmlspecialchars($rss_description),
            'RSS_DATE' => $this->fixPubDate(date("r", $now)),
        ));

        if (!empty($advertisement)) {
            foreach ($advertisement as $advert) {
                $url = $advert->url;
                if(!cot_url_check($url)) $url = COT_ABSOLUTE_URL.$url;

                $date = '';
                if(!empty($advert->created)){
                    $date = strtotime($advert->created);
                    $date += cot::$usr['timezone'] * 3600;
                    $date = date('r', $date);
                    $date = $this->fixPubDate($date);
                }

                $text = $advert->text;
                $textlength = intval(cot::$cfg['rss']['rss_pagemaxsymbols']);
                if ($textlength > 0 && mb_strlen($text) > $textlength) {
                    $text = cot_string_truncate($text, $textlength, true, false, cot::$R['advboard_cuttext']);
                }

                $t->assign(array(
                    'RSS_ROW_TITLE' => htmlspecialchars($advert->title),
                    'RSS_ROW_DESCRIPTION' => $this->convertRelativeUrls($text),
                    'RSS_ROW_DATE' => $date,
                    'RSS_ROW_LINK' => $url,
                    //'RSS_ROW_FIELDS' => $item['fields']
                ));
                $t->parse('MAIN.ITEM_ROW');
            }
        }

        $t->parse('MAIN');

//        ob_clean();
        header('Content-type: text/xml; charset=UTF-8');
        echo $t->text('MAIN');
        exit;
    }

    /**
     * Fixes timezone in RSS pubdate
     * @global array $usr
     * @param string $pubdate Pubdate generated with cot_date()
     * @return string Corrected pubdate
     */
    public function fixPubDate($pubdate) {

        $tz = floatval(cot::$usr['timezone']);
        $sign = $tz > 0 ? '+' : '-';
        $base = intval(abs($tz) * 100);
        $tz_str = $sign . str_pad($base, 4, '0', STR_PAD_LEFT);
        return str_replace('+0000', $tz_str, $pubdate);
    }

    public function convertRelativeUrls($text) {
        $text = preg_replace_callback('#(\s)(href|src)=("|\')?([^"\'\s>]+)(["\'\s>])#', 'adv_relative2absolute', $text);
        return $text;
    }
}

