@extends('layouts.master')
<?php
    $vision = "An open-source intelligence community promoting a decentralized economy of abundance for all humanity on earth by exposing suppressed knowledge.";
?>
@section('content')
        <div class="w3-container w3-padding w3-card-4 w3-round-large w3-margin w3-white">
          <p><strong>UNDER CONSTRUCTION</strong> {{$vision}}</p>
        </div>

        <!-- 3 landing page links  -->
        <div class="w3-container w3-margin-bottom">
          	
            <div class="w3-third w3-margin-bottom">
              	<div class="w3-round-large w3-white margin-right-desktop">
                    <a href="/join">
                        <img src="/data/imgs/join_link.jpg" class="w3-image w3-round-large">
                    </a>
                    <div class="w3-container w3-center">
                        <p>Utilizing onion-routing, hybrid-p2p architecture, and a mining-pool, we can financially promote and securely distribute suppressed information to the masses. Read more..</p>
                    </div>
            	</div>
            </div>
          
            <div class="w3-third w3-margin-bottom">
              	<div class="w3-round-large w3-white">
                    <a href="/secure-drop">
                        <img src="/data/imgs/secure-drop_link.jpg" class="w3-image w3-round-large">
                    </a>
                    <div class="w3-container w3-center">
                        <p>Anonymous whistle-blowers intelligence submission system...</p>
                    </div>
              	</div>
            </div>
			<div class="w3-third w3-margin-bottom">
            	<div class="w3-round-large w3-white margin-left-desktop">
                    <a href="/investigations">
                        <img src="/data/imgs/search_link.jpg" class="w3-image w3-round-large">
                    </a>
                    <div class="w3-container w3-center">
                        <p>Browse crowd-sourced investigations and evidence...</p>
                    </div>
              	</div>
            </div>
        </div>
        
@endsection