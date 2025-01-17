<?php
/**
 * Jetpack CRM Automation Transaction_Field condition.
 *
 * @package automattic/jetpack-crm
 */

namespace Automattic\Jetpack\CRM\Automation\Conditions;

use Automattic\Jetpack\CRM\Automation\Attribute_Definition;
use Automattic\Jetpack\CRM\Automation\Automation_Exception;
use Automattic\Jetpack\CRM\Automation\Base_Condition;
use Automattic\Jetpack\CRM\Automation\Data_Types\Data_Type;
use Automattic\Jetpack\CRM\Automation\Data_Types\Transaction_Data;
use Automattic\Jetpack\CRM\Entities\Transaction;

/**
 * Transaction_Field condition class.
 *
 * @since 6.2.0
 */
class Transaction_Field extends Base_Condition {

	/**
	 * Transaction_Field constructor.
	 *
	 * @since 6.2.0
	 *
	 * @param array $step_data The step data.
	 */
	public function __construct( array $step_data ) {
		parent::__construct( $step_data );

		// TODO: Fetch automation fields from our DAL.
		$transaction_fields = array(
			'status' => __( 'Status', 'zero-bs-crm' ),
			'type'   => __( 'Type', 'zero-bs-crm' ),
			'ref'    => __( 'Reference', 'zero-bs-crm' ),
			'title'  => __( 'Title', 'zero-bs-crm' ),
			'desc'   => __( 'Description', 'zero-bs-crm' ),
		);

		$this->valid_operators = array(
			'is'               => __( 'Is', 'zero-bs-crm' ),
			'is_not'           => __( 'Is not', 'zero-bs-crm' ),
			'contains'         => __( 'Contains', 'zero-bs-crm' ),
			'does_not_contain' => __( 'Does not contain', 'zero-bs-crm' ),
		);

		$this->set_attribute_definitions(
			array(
				new Attribute_Definition( 'field', __( 'Field', 'zero-bs-crm' ), __( 'Check this field against a specified value.', 'zero-bs-crm' ), Attribute_Definition::SELECT, $transaction_fields ),
				new Attribute_Definition( 'operator', __( 'Operator', 'zero-bs-crm' ), __( 'Determines how the field is compared to the specified value.', 'zero-bs-crm' ), Attribute_Definition::SELECT, $this->valid_operators ),
				new Attribute_Definition( 'value', __( 'Value', 'zero-bs-crm' ), __( 'Value to compare with the transaction field.', 'zero-bs-crm' ), Attribute_Definition::TEXT ),
			)
		);
	}

	/**
	 * Executes the condition. If the condition is met, the value stored in the
	 * attribute $condition_met is set to true; otherwise, it is set to false.
	 *
	 * @since 6.2.0
	 *
	 * @param Data_Type $data Data passed from the trigger.
	 * @return void
	 *
	 * @throws Automation_Exception If an invalid operator is encountered.
	 */
	protected function execute( Data_Type $data ) {
		/** @var Transaction $transaction */
		$transaction = $data->get_data();

		$field    = $this->get_attributes()['field'];
		$operator = $this->get_attributes()['operator'];
		$value    = $this->get_attributes()['value'];

		$this->check_for_valid_operator( $operator );
		$this->logger->log( 'Condition: ' . $field . ' ' . $operator . ' ' . $value . ' => ' . $transaction->{$field} );

		switch ( $operator ) {
			case 'is':
				$this->condition_met = ( $transaction->{$field} === $value );
				$this->logger->log( 'Condition met?: ' . ( $this->condition_met ? 'true' : 'false' ) );
				break;

			case 'is_not':
				$this->condition_met = ( $transaction->{$field} !== $value );
				$this->logger->log( 'Condition met?: ' . ( $this->condition_met ? 'true' : 'false' ) );
				break;

			case 'contains':
				$this->condition_met = ( strpos( $transaction->{$field}, $value ) !== false );
				$this->logger->log( 'Condition met?: ' . ( $this->condition_met ? 'true' : 'false' ) );
				break;

			case 'does_not_contain':
				$this->condition_met = ( strpos( $transaction->{$field}, $value ) === false );
				break;

			default:
				$this->condition_met = false;
				throw new Automation_Exception(
					/* Translators: %s is the unimplemented operator. */
					sprintf( __( 'Valid but unimplemented operator: %s', 'zero-bs-crm' ), $operator ),
					Automation_Exception::CONDITION_OPERATOR_NOT_IMPLEMENTED
				);
		}

		$this->logger->log( 'Condition met?: ' . ( $this->condition_met ? 'true' : 'false' ) );
	}

	/**
	 * Get the title for the transaction field condition.
	 *
	 * @since 6.2.0
	 *
	 * @return string The title 'Transaction Field Changed'.
	 */
	public static function get_title(): string {
		return __( 'Transaction Field', 'zero-bs-crm' );
	}

	/**
	 * Get the slug for the transaction field condition.
	 *
	 * @since 6.2.0
	 *
	 * @return string The slug 'transaction_field'.
	 */
	public static function get_slug(): string {
		return 'jpcrm/condition/transaction_field';
	}

	/**
	 * Get the description for the transaction field condition.
	 *
	 * @since 6.2.0
	 *
	 * @return string The description for the condition.
	 */
	public static function get_description(): string {
		return __( 'Checks if a transaction field matches an expected value', 'zero-bs-crm' );
	}

	/**
	 * Get the data type.
	 *
	 * @since 6.2.0
	 *
	 * @return string The type of the step.
	 */
	public static function get_data_type(): string {
		return Transaction_Data::class;
	}

	/**
	 * Get the category of the transaction field condition.
	 *
	 * @since 6.2.0
	 *
	 * @return string The category 'transaction'.
	 */
	public static function get_category(): string {
		return __( 'Transaction', 'zero-bs-crm' );
	}
}
