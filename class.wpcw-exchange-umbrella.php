<?php
/**
 * Main connector class.
 *
 * @author    Iron Bound Designs
 * @since     1.0
 * @license   GPLv2 or Later
 * @copyright Iron Bound Designs, 2015.
 */

if ( class_exists( 'WPCW_Exchange' ) ):
	/**
	 * Class WPCW_Exchange_Umbrella
	 */
	class WPCW_Exchange_Umbrella extends WPCW_Exchange {

		/**
		 * Add our hooks.
		 */
		protected function attach_updateUserCourseAccess() {
			parent::attach_updateUserCourseAccess();

			add_action( 'itegms_create_relationship', array( $this, 'grant_access_to_new_umbrella_member' ) );
			add_action( 'itegms_delete_relationship', array( $this, 'revoke_access_from_deleted_umbrella_member' ) );
		}

		/**
		 * Grant access to a course when a member is added to an umbrella membership.
		 *
		 * @since 1.0
		 *
		 * @param \ITEGMS\Relationship\Relationship $relationship
		 */
		public function grant_access_to_new_umbrella_member( \ITEGMS\Relationship\Relationship $relationship ) {
			$user = $relationship->get_member();

			$memberLevels = it_exchange_get_customer_products( $user->id );

			$userLevels = array();

			foreach ( $memberLevels as $key => $memberLevel ) {
				$userLevels[ $key ] = $memberLevel['product_id'];
			}

			$userLevels[] = $relationship->get_purchase()->get_membership()->ID;

			// Over to the parent class to handle the sync of data.
			parent::handle_courseSync( $user->id, $userLevels );
		}

		/**
		 * Revoke access to a course when a member is removed from an umbrella membership.
		 *
		 * @since 1.0
		 *
		 * @param \ITEGMS\Relationship\Relationship $relationship
		 */
		public function revoke_access_from_deleted_umbrella_member( \ITEGMS\Relationship\Relationship $relationship ) {

			$user = $relationship->get_member();

			$memberLevels = it_exchange_get_customer_products( $user->id );

			$userLevels = array();

			foreach ( $memberLevels as $key => $memberLevel ) {
				$userLevels[ $key ] = $memberLevel['product_id'];
			}

			parent::handle_courseSync( $user->id, $userLevels );
		}

		/**
		 * Retroactively assign access to a product.
		 *
		 * @since 1.0
		 *
		 * @param int $level_ID
		 */
		protected function retroactive_assignment( $level_ID ) {

			$query = new \ITEGMS\Purchase\Purchase_Query( array(
				'membership' => $level_ID
			) );

			/** @var \ITEGMS\Purchase\Purchase[] $purchases */
			$purchases = $query->get_results();

			foreach ( $purchases as $purchase ) {
				$relationships = $purchase->get_members();

				foreach ( $relationships as $relationship ) {
					$this->grant_access_to_new_umbrella_member( $relationship );
				}
			}

			parent::retroactive_assignment( $level_ID );
		}
	}
endif;