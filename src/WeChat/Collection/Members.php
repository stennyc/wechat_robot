<?php
namespace Im050\WeChat\Collection;

/**
 * Class Members
 *
 * @package Im050\WeChat\Collection
 */
class Members
{

    const TYPE_GROUP = 'group';

    const TYPE_OFFICIAL = 'official';

    const TYPE_CONTACT = 'contact';

    const TYPE_SPECIAL = 'special';

    protected static $_instance = null;

    public $officials = null;

    public $contacts = null;

    public $groups = null;

    public $specials = null;

    public static $special_username = ['newsapp', 'fmessage', 'filehelper', 'weibo', 'qqmail',
        'fmessage', 'tmessage', 'qmessage', 'qqsync', 'floatbottle', 'lbsapp', 'shakeapp',
        'medianote', 'qqfriend', 'readerapp', 'blogapp', 'facebookapp', 'masssendapp',
        'meishiapp', 'feedsapp', 'voip', 'blogappweixin', 'weixin', 'brandsessionholder',
        'weixinreminder', 'wxid_novlwrv3lqwv11', 'gh_22b87fa7cb3c', 'officialaccounts',
        'notification_messages', 'wxid_novlwrv3lqwv11', 'gh_22b87fa7cb3c', 'wxitil',
        'userexperience_alarm', 'notification_messages'
    ];

    private function __construct()
    {
        $var = ['specials', 'contacts', 'groups', 'officials'];
        foreach ($var as $key => $val) {
            $this->$val = new ContactCollection();
        }
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function push($item)
    {
        switch (Members::getUserType($item)) {
            case Members::TYPE_CONTACT:
                $list = $this->contacts;
                break;
            case Members::TYPE_GROUP:
                $list = $this->groups;
                break;
            case Members::TYPE_SPECIAL:
                $list = $this->specials;
                break;
            case Members::TYPE_OFFICIAL:
                $list = $this->officials;
                break;
            default:
                throw new \Exception("未能识别的用户类型");
        }

        $list->put($item['UserName'], $item);
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function getSpecials()
    {
        return $this->specials;
    }

    public function getContacts()
    {
        return $this->contacts;
    }

    public function getOfficials()
    {
        return $this->officials;
    }

    public function getContactByUserName($username)
    {
        if (substr($username, 0, 2) == '@@') {
            return $this->getGroups()->getContactByUserName($username);
        } else {
            if (($user = $this->getContacts()->getContactByUserName($username)) !== null) {
                return $user;
            } else if (($user = $this->getOfficials()->getContactByUserName($username)) !== null) {
                return $user;
            } else if (($user = $this->getSpecials()->getContactByUserName($username)) !== null) {
                return $user;
            } else {
                return null;
            }
        }
    }

    public static function getUserType($item)
    {
        if (substr($item['UserName'], 0, 2) == "@@") {
            return Members::TYPE_GROUP;
        } else {
            if (($item['VerifyFlag'] & 8) != 0) {
                if (in_array($item['UserName'], self::$special_username)) {
                    return Members::TYPE_SPECIAL;
                } else {
                    return Members::TYPE_OFFICIAL;
                }
            } else {
                return Members::TYPE_CONTACT;
            }
        }
    }

}