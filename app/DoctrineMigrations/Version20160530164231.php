<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160530164231 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE conf_cambio_hora');
        $this->addSql('DROP TABLE conf_fecha_sistema');
        $this->addSql('ALTER TABLE d_bitacora_almacen ADD qty INT DEFAULT NULL');
        $this->addSql('CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetSerialesProductoEnAlmacenObjectsPlus`(IN `warehouse_id` INT, IN `product_id` INT)
    NO SQL
BEGIN

declare montoPositivo integer;

declare montoNegativo integer;

DROP TEMPORARY TABLE IF EXISTS  bitacoras_serial_producto_almacen;
CREATE TEMPORARY TABLE IF NOT EXISTS bitacoras_serial_producto_almacen
SELECT *  from d_bitacora_serial b
WHERE b.product_id = product_id and b.warehouse_id= warehouse_id ;


 select *, sum(cantidad) as cant
from bitacoras_serial_producto_almacen
group by serial
having cant>0;

END');
        $this->addSql('CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetSerialesProductoEnAlmacenPlus`(IN `warehouse_id` INT, IN `product_id` INT)
    NO SQL
BEGIN

declare montoPositivo integer;

declare montoNegativo integer;


DROP TEMPORARY TABLE IF EXISTS  bitacoras_serial_producto_almacen;
CREATE TEMPORARY TABLE IF NOT EXISTS bitacoras_serial_producto_almacen
SELECT cantidad , serial  from d_bitacora_serial b
WHERE b.product_id = product_id and b.warehouse_id= warehouse_id ;

select res.serial from
(
 select serial, sum(cantidad) as cant
from bitacoras_serial_producto_almacen
group by serial
having cant>0
 ) res ;


END');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE conf_cambio_hora (id INT AUTO_INCREMENT NOT NULL, hora TIME DEFAULT NULL, activo TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conf_fecha_sistema (id INT AUTO_INCREMENT NOT NULL, fecha DATE DEFAULT NULL, activo TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE d_bitacora_almacen DROP qty');
        $this->addSql('DROP PROCEDURE `sp_GetSerialesProductoEnAlmacenObjectsPlus`');
        $this->addSql('DROP PROCEDURE `sp_GetSerialesProductoEnAlmacenPlus`');
    }
}
