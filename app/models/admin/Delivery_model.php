<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_model extends My_Model
{
    public function __construct()
    {
        parent::__construct();

	    $this->load->admin_model( 'delivery_schedule_model', 'schedule' );
	    $this->load->admin_model('sales_model');

    }

	public function getAllInvoiceItems($sale_id, $return_id = null)
	{
		$this->db->select('sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.image, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name, products.unit as base_unit_id, units.code as base_unit_code')
		         ->join('products', 'products.id=sale_items.product_id', 'left')
		         ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
		         ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
		         ->join('units', 'units.id=products.unit', 'left')
		         ->group_by('sale_items.id')
		         ->order_by('id', 'asc');
		if ($sale_id && !$return_id) {
			$this->db->where('sale_id', $sale_id);
		} elseif ($return_id) {
			$this->db->where('sale_id', $return_id);
		}
		$q = $this->db->get('sale_items');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAllInvoiceItemsForShipment( $shipment_id )
	{
		/*"SELECT rerp_package.shipment_id, package_item.*, `rerp_tax_rates`.`code` as `tax_code`, `rerp_tax_rates`.`name` as `tax_name`, `rerp_tax_rates`.`rate` as `tax_rate`, `rerp_products`.`image`, `rerp_products`.`details` as `details`, `rerp_product_variants`.`name` as `variant`, `rerp_products`.`hsn_code` as `hsn_code`, `rerp_products`.`second_name` as `second_name`, `rerp_products`.`unit` as `base_unit_id`, `rerp_units`.`code` as `base_unit_code`
			FROM rerp_package 
			JOIN (
					SELECT rerp_package_items.package_id, rerp_sale_items.* 
					FROM rerp_package_items 
					JOIN rerp_sale_items ON rerp_sale_items.id = rerp_package_items.sales_item_id
				 ) package_item ON package_item.package_id = rerp_package.id 
                 
            LEFT JOIN `rerp_products` ON `rerp_products`.`id`= package_item.product_id 
            LEFT JOIN `rerp_product_variants` ON rerp_product_variants.id = package_item.option_id 
            LEFT JOIN `rerp_tax_rates` ON `rerp_tax_rates`.`id`= package_item.tax_rate_id 
            LEFT JOIN `rerp_units` ON `rerp_units`.`id`=`rerp_products`.`unit`
            
			WHERE rerp_package.shipment_id = 158
			GROUP BY package_item.`id` ORDER BY package_item.`id` ASC
		"*/
		$t_package_items  = $this->db->dbprefix( 'package_items' );
		$t_sale_items    = $this->db->dbprefix( 'sale_items' );
		$package_item = $this->db->dbprefix( 'package_item' );

		$ipac = "( SELECT {$t_package_items}.package_id, {$t_sale_items}.*
					FROM {$t_package_items} 
					JOIN {$t_sale_items} ON {$t_sale_items}.id = {$t_package_items}.sales_item_id
				 ) {$package_item} ";

		$this->db->select('package.shipment_id, package_item.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.image, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name, products.unit as base_unit_id, units.code as base_unit_code')
		         ->join($ipac, 'package_item.package_id = package.id', 'left')
		         ->join('products', 'products.id = package_item.product_id', 'left')
		         ->join('product_variants', 'product_variants.id = package_item.option_id', 'left')
		         ->join('tax_rates', 'tax_rates.id = package_item.tax_rate_id', 'left')
		         ->join('units', 'units.id = products.unit', 'left')
		         ->group_by('package_item.id')
		         ->order_by('package_item.id', 'asc');
		$this->db->where('package.shipment_id', $shipment_id);
		$q = $this->db->get('package');

		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAllInvoiceItemsWithDetails($sale_id)
	{
		$this->db->select('sale_items.*, products.details, product_variants.name as variant');
		$this->db->join('products', 'products.id=sale_items.product_id', 'left')
		         ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
		         ->group_by('sale_items.id');
		$this->db->order_by('id', 'asc');
		$q = $this->db->get_where('sale_items', ['sale_id' => $sale_id]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
	}

	public function getAllSalesReferenceNo()
	{
		$this->db->select( '' . $this->db->dbprefix( 'sales' ) . '.id, '. $this->db->dbprefix( 'sales' ) . '.reference_no' )
		         ->join( 'shipment', 'shipment.sale_id = sales.id', 'left' )
		         ->where( 'shipment.sale_id IS null' );
		$q = $this->db->get( 'sales' );
		if ( $q->num_rows() > 0 ) {
			foreach ( ( $q->result() ) as $row ) {
				$data[] = $row;
			}

			return $data;
		}

		return FALSE;
	}

	public function getAllPendingDeliveryRefNo()
	{
		$this->db->select( 'id, do_reference_no, sale_id, sale_reference_no, customer' )
		         ->where( 'status', 'pending' );
		$q = $this->db->get( 'deliveries' );
		if ( $q->num_rows() > 0 ) {
			foreach ( ( $q->result() ) as $row ) {
				$data[] = $row;
			}

			return $data;
		}

		return FALSE;
	}

	public function getAllPendingDeliveryRefNoForPickup()
	{
		$this->db->select( 'deliveries.id as id, deliveries.do_reference_no, deliveries.sale_id, deliveries.sale_reference_no, deliveries.customer' )
				 ->join('(SELECT delivery_id FROM '. $this->db->dbprefix('pickup'). ' WHERE delete_flag = 0 GROUP BY delivery_id) pickup', 'pickup.delivery_id = deliveries.id', 'LEFT')
				 ->join('(SELECT delivery_id FROM '. $this->db->dbprefix('shipment'). ' WHERE delete_flag = 0 GROUP BY delivery_id) shipment', 'shipment.delivery_id = deliveries.id', 'LEFT')
		         ->where( 'deliveries.status', 'pending' )
		         ->where( 'pickup.delivery_id' )
		         ->where( 'shipment.delivery_id' );
		$q = $this->db->get( 'deliveries' );
		if ( $q->num_rows() > 0 ) {
			foreach ( ( $q->result() ) as $row ) {
				$data[] = $row;
			}

			return $data;
		}

		return FALSE;
	}

    public function getSaleSuggestions($term, $limit = 5)
	{
		$this->db->select('' . $this->db->dbprefix('sales') . '.id, reference_no, ' . $this->db->dbprefix('sales') . '.customer as name')
		         ->where('(' . $this->db->dbprefix('sales') . ".id LIKE '%" . $term . "%' OR reference_no LIKE '%" . $term . "%')")
		         ->limit($limit);
		$q = $this->db->get('sales');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

    public function addDelivery($data = [])
    {
	    if ( $this->db->insert( 'deliveries', $data ) ) {
	    	$id = $this->db->insert_id();
            if ($this->site->getReference('do') == $data['do_reference_no']) {
                $this->site->updateReference('do');
            }
            return $id;
        }
        return false;
    }

    public function deleteDelivery($id)
    {
        if ($this->db->delete('deliveries', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getDeliveryByID($id)
    {
        $q = $this->db->get_where('deliveries', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getDeliveryBySaleID($sale_id)
    {
        $q = $this->db->get_where('deliveries', ['sale_id' => $sale_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getDeliveryScheduleBySaleId( $sale_id ) {
		$q = $this->db->get_where( 'delivery_schedules', [ 'sales_id' => $sale_id ], 1 );
		if ( $q->num_rows() > 0 ) {
			return $q->row();
		}
		
		return false;
	}

    public function updateDelivery($id, $data = [])
    {
        if ($this->db->update('deliveries', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    //ALL Packaging methods =======================================
	public function getAllPackageBySaleID( $sale_id = false ) {
		if ( $sale_id ) {
			$this->db->select( '*' )->order_by( 'id', 'asc' );
			$this->db->where( 'sale_id', $sale_id );
			$this->db->where( 'delete_flag', 0 );
			$q = $this->db->get( 'package' );
			if ( $q->num_rows() > 0 ) {
				foreach ( ( $q->result() ) as $row ) {
					$data[] = $row;
				}
				return $data;
			}
		}
		return FALSE;
	}

	public function getAllPackageByShipmentID( $shipment_id = false ) {
		if ( $shipment_id ) {
			$this->db->select( '*' )->order_by( 'id', 'asc' );
			$this->db->where( 'shipment_id', $shipment_id );
			$this->db->where( 'delete_flag', 0 );
			$q = $this->db->get( 'package' );
			if ( $q->num_rows() > 0 ) {
				$data = [];
				foreach ( ( $q->result() ) as $row ) {
					$data[] = $row;
				}
				return $data;
			}
		}
		return FALSE;
	}

	public function getAllPackedItemsIDBySaleID( $sale_id = false ) {
	    if ( $sale_id ) {
	        $this->db->select( '*' )->order_by( 'id', 'asc' );
	        $this->db->where( 'sale_id', $sale_id );
	        $q = $this->db->get( 'package_items' );
	        if ( $q->num_rows() > 0 ) {
	            foreach ( ( $q->result() ) as $row ) {
	                $data[] = $row->sales_item_id;
	            }
	            return $data;
	        }
	    }
	    return array();
	}

	public function getAllPackageItemsIDByPackageID( $package_id = false ) {
		if ( $package_id ) {
			$this->db->select( '*' )->order_by( 'id', 'asc' );
			$this->db->where( 'package_id', $package_id );
			$q = $this->db->get( 'package_items' );
			if ( $q->num_rows() > 0 ) {
				foreach ( ( $q->result() ) as $row ) {
					$data[] = $row->sales_item_id;
				}
				return $data;
			}
		}
		return array();
	}

	public function getUnpackedItemsBySaleID( $sale_id = false ) {
		if( $sale_id ){
		    $pack = $this->getAllPackedItemsIDBySaleID( $sale_id );
			$items = $this->getAllInvoiceItems($sale_id);

			foreach ($items as $item){
				$as_items[] = $item->id;
			}

			$result = array_diff($as_items, $pack);
			
			if( count( $result ) ){
				return $result;
			}
			else{
                return array();
			}
		}
		return false;
	}

	public function getReturnItemsByPackage_id( $package_id = NULL ) {
		$t_package_items = $this->db->dbprefix( 'package_items' );
		$t_sale_items    = $this->db->dbprefix( 'sale_items' );
		$t_package       = $this->db->dbprefix( 'package' );
		$package_item    = $this->db->dbprefix( 'package_item' );
		$re_pack         = $this->db->dbprefix( 're_pack' );

		$ipac = "( SELECT {$package_item}.product_id
		       FROM {$t_package}
		       JOIN ( SELECT {$t_package_items}.package_id, {$t_sale_items}.product_id 
		              FROM {$t_package_items}
		              JOIN {$t_sale_items} ON {$t_sale_items}.id = {$t_package_items}.sales_item_id
		            ) $package_item ON {$package_item}.package_id = {$t_package}.id
		       WHERE {$t_package}.id = {$package_id}
		     ) $re_pack ";

		$this->db->select( 'sale_items.*' )
		         ->join( 'sales', 'sales.id = package.sale_id' )
		         ->join( 'sale_items', 'sale_items.sale_id = sales.return_id' )
		         ->join( $ipac, 're_pack.product_id = sale_items.product_id' )
		         ->where( 'package.id', $package_id )
		         ->order_by( 'sale_items.product_id', 'asc' );
		$q = $this->db->get( 'package' );

		if ( $q->num_rows() > 0 ) {
			foreach ( ( $q->result() ) as $row ) {
				$data[] = $row;
			}

			return $data;
		}

		return FALSE;
	}

	/**
	 * @param int $package_id
	 *
	 * @return array|bool
	 */
	public function getPackedItemsByPackageID( $package_id = false ) {
		if ( $package_id ) {
			$data = array();
			$this->db->select( '*' )->order_by( 'id', 'asc' );
			$this->db->where( 'package_id', $package_id );
			$q = $this->db->get( 'package_items' );
			if ( $q->num_rows() > 0 ) {
				foreach ( ( $q->result() ) as $row ) {
					$data[] = $this->getInvoiceItemByID( $row->sales_item_id );
				}
				return $data;
			}
		}
		return FALSE;
	}

	public function getAllShipmentByDeliveryID ( $delivery_id ) {
		if ( $delivery_id ) {
			$this->db->select( '*' )->order_by( 'id', 'asc' );
			$this->db->where( 'delivery_id', $delivery_id );
			$this->db->where( 'delete_flag', 0 );
			$q = $this->db->get( 'shipment' );
			if ( $q->num_rows() > 0 ) {
				foreach ( ( $q->result() ) as $row ) {
					$data[] = $row;
				}
				return $data;
			}
		}
		return FALSE;
	}

	public function getShipmentCostingBYShipment_id ( $shipment_id = NULL ){
		if ( $shipment_id ) {
			$p_item = "(SELECT {$this->db->dbprefix( 'package_items' )}.package_id, {$this->db->dbprefix( 'sale_items' )}.* FROM {$this->db->dbprefix( 'package_items' )} JOIN {$this->db->dbprefix( 'sale_items' )} ON {$this->db->dbprefix( 'sale_items' )}.id = {$this->db->dbprefix( 'package_items' )}.sales_item_id) package_item";

			$this->db->select( 'package.shipment_id, SUM(package_item.subtotal) as total' );
			$this->db->join( $p_item, 'package_item.package_id = package.id' );
			$this->db->where( 'package.shipment_id', $shipment_id );
			$this->db->where( 'package.delete_flag', 0 );
			$this->db->group_by( 'package.shipment_id', 0 );

			$q = $this->db->get( 'package' );
			if ( $q->num_rows() > 0 ) {
				foreach ( ( $q->result() ) as $row ) {
					$data = $row;
				}
				return $data;
			}
		}
		return FALSE;
	}

	public function getPackageCostingBYPackage_id ( $package_id = NULL ){
		if ( $package_id ) {
			$pp_item = "(SELECT {$this->db->dbprefix( 'package_items' )}.package_id, {$this->db->dbprefix( 'sale_items' )}.* FROM {$this->db->dbprefix( 'package_items' )} JOIN {$this->db->dbprefix( 'sale_items' )} ON {$this->db->dbprefix( 'sale_items' )}.id = {$this->db->dbprefix( 'package_items' )}.sales_item_id) package_item";

			$this->db->select( 'package.id as package_id, SUM(package_item.subtotal) as total' );
			$this->db->join( $pp_item, 'package_item.package_id = package.id' );
			$this->db->where( 'package.id', $package_id );
			$this->db->where( 'package.delete_flag', 0 );
			$this->db->group_by( 'package.id', 0 );

			$q = $this->db->get( 'package' );
			if ( $q->num_rows() > 0 ) {
				foreach ( ( $q->result() ) as $row ) {
					$data = $row;
				}
				return $data;
			}
		}
		return FALSE;
	}

	public function getShippingCostAdjustmentBYSale_id ( $sale_id = NULL ){
		if ( $sale_id ) {
			$data = 0;
			$this->db->select( 'SUM(cost_adjustment) as cost' );
			$this->db->where( 'sale_id', $sale_id );
			$this->db->where( 'delete_flag', 0 );
			$this->db->group_by( 'sale_id', 0 );

			$q = $this->db->get( 'shipment' );
			if ( $q->num_rows() > 0 ) {
				foreach ( ( $q->result() ) as $row ) {
					$data = $row;
				}
				return $data->cost;
			}
			return $data;
		}
		return FALSE;
	}

	public function getAllPickupByDeliveryID ( $delivery_id ) {
		if ( $delivery_id ) {
			$this->db->select( '*' )->order_by( 'id', 'asc' );
			$this->db->where( 'delivery_id', $delivery_id );
			$this->db->where( 'delete_flag', 0 );
			$q = $this->db->get( 'pickup' );
			if ( $q->num_rows() > 0 ) {
				foreach ( ( $q->result() ) as $row ) {
					$data[] = $row;
				}
				return $data;
			}
		}
		return FALSE;
	}

	/**
	 * @param int $item_id
	 *
	 * @return array|bool
	 */
	public function getInvoiceItemByID( $item_id ) {
		$this->db->select( 'sale_items.*, tax_rates.code as tax_code, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.image, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name, products.unit as base_unit_id, units.code as base_unit_code' )
		         ->join( 'products', 'products.id=sale_items.product_id', 'left' )
		         ->join( 'product_variants', 'product_variants.id=sale_items.option_id', 'left' )
		         ->join( 'tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left' )
		         ->join( 'units', 'units.id=products.unit', 'left' )
		         ->group_by( 'sale_items.id' );

		$this->db->where( 'sale_items.id', $item_id );
		$q = $this->db->get( 'sale_items' );
		if ( $q->num_rows() > 0 ) {
			return $q->result();
		}
		return FALSE;
	}
}
