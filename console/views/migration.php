<?php
/**
 * This view is used by console/controllers/MigrateController.php.
 *
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */

echo "<?php\n";
if (!empty($namespace)) {
    echo "\nnamespace {$namespace};\n";
}
?>

use yii\db\Migration;

/**
* Class <?= $className . "\n" ?>
*/
class <?= $className ?> extends Migration
{
/**
* {@inheritdoc}
*/
public function safeUp()
{
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
}

/**
* {@inheritdoc}
*/
public function safeDown()
{
echo "<?= $className ?> cannot be reverted.\n";

return false;
}

/*
// Use up()/down() to run migration code without a transaction.
public function up()
{
    $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
        // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
}

public function down()
{
echo "<?= $className ?> cannot be reverted.\n";

return false;
}
*/
}
