<?php

namespace App\Traits;

trait BagOperations
{
    /**
     * See if a bag can transition to the given state, apply and save if possible
     *
     * Send an ErrorEvent if the transition could not be applied.
     *
	 * @param  string  $url
	 * @return $this
	 */

    private function tryBagTransition( $bag, $transitionTo ) : bool
    {
        if( ! $bag->canTransition( $transitionTo ) ) {
            //Todo: Separate event handling for transition errors
            event( new \App\Events\ErrorEvent( $bag ) );
            return false;
        }

        $bag->applyTransition( $transitionTo )->save();
        return true;
    }


}
