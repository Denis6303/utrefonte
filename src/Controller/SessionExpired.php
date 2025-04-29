<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SessionExpired
 *
 * @author edem
 */
class SessionExpired {
    //put your code here
    public function SessionExpired() {
        
        return $this->render('utbClientBundle/Client/sessionExpired.html.twig', array('locale' => $locale));
        
    }
}
