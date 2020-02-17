<?php

$tree = [
    'Table Title' => [
        1 => [
            4 => [],
            5 => []
        ],
        2 => [
            6 => [],
            7 => [
                11 => []
            ],
            8 => [
                12 => [
                    14 => [],
                    15 => []
                ]
            ],
        ],
        3 => [
            9  => [],
            10 => [
                13 => []
            ]
        ],
		4=>[]
    ]
];

// Loop over the tree. Every leaf in the root of the tree
// gets his own table(s).
foreach ($tree as $name => $leaves) { 
    $table = [];
    parseLeaf($name, $leaves, $table);
    $table = cleanupRows($table);

    output($table);
	
}

/**
 * Convert a leaf in the tree to an array to be used to print the tables.
 * The span of a leaf is either the sum of its children's spans,
 * or 1 if it has no children.
 *
 * @param string $name
 * @param array  $leaves
 * @param array  $table
 * @param int    $level
 * @param int    $position
 *
 * @return int
 */
function parseLeaf($name, $leaves, &$table, $level = 0, $position = 0)
{   
    if (!empty($leaves)) {
        $span = 0;

        foreach ($leaves as $leafName => $childLeaves) {
            $span += parseLeaf(
                $leafName,
                $childLeaves,
                $table,
                $level + 1,
                $position + $span
            );
			echo $span;
        }
    } else {
        $span = 1;
    }
    
    $table[$level][$position] = getCell($name, $span);

    return $span;
}
exit;
/**
 * Insert empty cells where needed and sort by keys.
 *
 * @param array $table
 *
 * @return array
 */
function cleanupRows($table)
{
    $width = $table[0][0]['span'];

    foreach ($table as $rowNumber => $row) {
        $spanSoFar = 0;
        foreach ($row as $position => $cell) {
            addExtraCells($table, $spanSoFar, $rowNumber, $position);
            $spanSoFar += $cell['span'];
        }
        addExtraCells($table, $spanSoFar, $rowNumber, $width);
        ksort($table[$rowNumber]);
    }
    ksort($table);

    return $table;
}

/**
 * @param array $table
 * @param int   $spanSoFar
 * @param int   $rowNumber
 * @param int   $position
 */
function addExtraCells(&$table, &$spanSoFar, $rowNumber, $position)
{
    while ($spanSoFar < $position) {
        $table[$rowNumber][$spanSoFar] = getCell();
        $spanSoFar += 1;
    }
}

/**
 * @param string $name
 * @param int    $span
 *
 * @return array
 */
function getCell($name = '', $span = 1)
{
    return ['name' => $name, 'span' => $span];
}

/**
 * Print the table.
 *
 * @param array $table
 */
function output($table)
{
    echo '<table border="1" width="100%">';
    foreach ($table as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo '<td colspan="' . $cell['span'] . '" align="center">';
            echo $cell['name'];
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}