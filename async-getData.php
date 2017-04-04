<?php
function custom_unset(&$array=array(), $key=0) {
    if(isset($array[$key])){
        unset($array[$key]);
        $array = array_values($array);
    }
    return $array;
}

function explodeTree($array, $delimiter = '_', $baseval = false)
{
    if(!is_array($array)) return false;
    $splitRE   = '/' . preg_quote($delimiter, '/') . '/';
    $returnArr = array();
    foreach ($array as $key => $val) {
        // Get parent parts and the current leaf
        $parts  = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
        $leafPart = array_pop($parts);

        // Build parent structure
        // Might be slow for really deep and large structures
        $parentArr = &$returnArr;
        foreach ($parts as $part) {
            if (!isset($parentArr[$part])) {
                $parentArr[$part] = array();
            } elseif (!is_array($parentArr[$part])) {
                if ($baseval) {
                    $parentArr[$part] = array('__base_val' => $parentArr[$part]);
                } else {
                    $parentArr[$part] = array();
                }
            }
            $parentArr = &$parentArr[$part];
        }

        // Add the final part to the structure
        if (empty($parentArr[$leafPart])) {
            $parentArr[$leafPart] = $val;
        } elseif ($baseval && is_array($parentArr[$leafPart])) {
            $parentArr[$leafPart]['__base_val'] = $val;
        }
    }
    return $returnArr;
}

ini_set("auto_detect_line_endings", true);

$newFilename = "uploads/".$_POST["filename"];

$handle = fopen($newFilename, "r");
$data = [];

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $data[] = str_getcsv($line, ',');
    }
}
fclose($handle);

$pos = 0;
while ($pos < count($data)) {
    $is_empty = TRUE;
    for ($j = 0; $j < count($data[$pos]); $j++) {
        if ($data[$pos][$j] != null) {
            $is_empty = FALSE;
            break;
        }
    }

    if ($is_empty) {
        $data = custom_unset($data, $pos);
    } else {
        $pos++;
    }
}

// remove empty cols
$pos = count($data[0]) - 1;
while ($pos >= 0) {
    $is_empty = TRUE;
    for ($i = 0; $i < count($data); $i++) {
        if ($data[$i][$pos] != null) {
            $is_empty = FALSE;
            break;
        }
    }
    if ($is_empty) {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = custom_unset($data[$i], $pos);
        }
    }
    $pos--;
}

$cntCategoryLevel = 0;
$cntMonth = 0;
$cntCol = count($data[0]);
$cntRow = count($data);
for ($i = 0; $i < $cntCol; $i++) {
    if ($data[0][$i] == null) {
        $cntCategoryLevel++;
    } else {
        break;
    }
}
$cntMonth = $cntCol - $cntCategoryLevel;

$arrMonthName = [];

for ($i = $cntCategoryLevel; $i < $cntCol; $i++) {
    $arrMonthName[] = (strtotime($data[0][$i]) == '') ?
                    $data[0][$i] :
                    date('m-d', strtotime($data[0][$i]));
}

$arrTempCategories = [];
$arrResult = [];
$arrColumn = [];
for ($i = 1; $i < $cntRow; $i++) {
    $strTreeKey = "";
    for ($j = 0; $j < $cntCategoryLevel; $j++) {
        if ($data[$i][$j] == null) {
            $strTreeKey .= $arrTempCategories[$j]."||";
        } else {
            $arrTempCategories[$j] = $data[$i][$j];
            $strTreeKey .= $arrTempCategories[$j];
            if ($data[$i][$cntCategoryLevel] != null) {
                $arrColumn[] = $data[$i][$j];
            }
            break;
        }
    }

    $is_empty = TRUE;
    for ($j = 0; $j < $cntMonth; $j++) {
        if ($data[$i][$j + $cntCategoryLevel] != null) {
            $is_empty = FALSE;
            break;
        }
    }
    if ($is_empty) {
        $treeData = null;
    } else {
        $treeData = [];
        for ($j = 0; $j < $cntMonth; $j++) {
            $treeData[$arrMonthName[$j]] = $data[$i][$j + $cntCategoryLevel];
        }
    }

    $arrResult[$strTreeKey] = $treeData;
}

$arr = explodeTree($arrResult, '||', true);
echo json_encode(['data' => $arr, 'column' => $arrColumn, ]);
exit();