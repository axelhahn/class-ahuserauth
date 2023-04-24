<?php

/**
 * get html code for a login form
 * @param  string  $sType  type of authentication method; one of file|...
 * @return string
 */
function showLoginForm($sType){
    $sReturn='';
    $sReturn.='

    <form class="pure-form pure-form-aligned" method="POST" action="?">
    <input type="hidden" name="type" value="'.$sType.'">
    <fieldset>
        <div class="pure-control-group">
            <label for="aligned-name">Username</label>
            <input type="text" id="aligned-name" placeholder="Username" name="username" />
            <span class="pure-form-message-inline">This is a required field.</span>
        </div>
        <div class="pure-control-group">
            <label for="aligned-password">Password</label>
            <input type="password" id="aligned-password" placeholder="Password" name="password" />
        </div>
        <div class="pure-controls">
            <button onclick="history.back(); return false;" class="pure-button">Back</button>
            <button type="submit" class="pure-button pure-button-primary">Submit</button>
        </div>
    </fieldset>
    </form>
    ';

    return $sReturn;
}