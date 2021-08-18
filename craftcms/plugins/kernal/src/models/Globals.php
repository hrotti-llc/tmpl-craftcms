<?php

namespace hrotti\kernal\models;

use Craft;

use craft\elements\Entry;

use craft\base\Component;
use craft\web\Request;

use craft\db\Query;

class Globals extends Entry {

    use \hrotti\kernal\helpers\CommonModelHelper;

    public function rules() {

        return [
            [[], 'safe']
        ];

    }

    static public function query(
        $handle = 'basics', 
        $filters = null
    ) {

        $globals = $entries ?? self::$plugin->helpers->globals();
        $query = $globals->with([])->handle($handle);

        return $query;

    }

    static public function normalizeEntry(
        object $entry
    ) {

        $item = [

        ];

        return $item;
    
    }

}
