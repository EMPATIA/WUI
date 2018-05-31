<!-- GOALS -->
<section style=" z-index: 1000">
    <div class="container-fluid goals-menu-container" style="">
        <div class="row menus-row " style="">
            <div class="col-sm-6 col-sm-offset-3 menus-line goals" style=""><i class="fa fa-bullseye" style="color: #b3b3b3"></i>{{trans('home.goals')}}</div>
        </div>
    </div>
    <div class="container-fluid goals-container" >
        <div class="row goals-rows" >
            @if(!empty($homePageConfigurations) && property_exists($homePageConfigurations,'goals'))
                <?php $i=0; ?>
                @foreach($homePageConfigurations->goals as $goal)
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 goals-cols" >
                        <div class="col-sm-3 col-md-4 col-lg-5 goals-icon">
                            @if(isset($goalIcon[$i]))
                                <img src="{{asset('images/empatia/'.$goalIcon[$i].'.png')}}" alt=""/>
                            @endif
                        </div>
                        <div class="col-sm-9 col-md-8 col-lg-7 goals">
                            <h3>{{ (isset($goal->goal_title) ? $goal->goal_title : null)}}</h3>
                            <p>{{(isset($goal->goal_description) ? $goal->goal_description : null)}}</p>
                        </div>
                    </div>
                    <?php $i++; ?>
                @endforeach
            @endif
        </div>
    </div>
</section>
