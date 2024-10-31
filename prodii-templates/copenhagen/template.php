<?php
/*
 * Template Name: Copenhagen
 * Template Slug: prodii-copenhagen
 * Scripts: assets/scripts.js, assets/jquery.ellipsis.min.js
 * Styles: assets/css/font-awesome.min.css, assets/css/style.css
 * Version: 1.0.0
 */
?>
<?php
$data = prodii_template_data();
$type = prodii_template_type();
$args = prodii_template_args();
?>

<?php if ($type === Prodii::TEAM) : ?>
    <div class="prd-teams">
        <?php foreach ($data as $team) : ;
            $company = $team->get_company(); ?>
            <div class="prd-body">
                <div id="prd-breadcrumb" class="prd-pull-left">
                    <span class="prd-text-white"><?php echo $company->get('name') ?></span>
                    <span class="prd-text-grey prd-padding-left-5 prd-padding-right-5">/</span>
                    <span class="prd-text-white"><?php echo $team->get('name') ?></span>
                </div>

                <ul class="prd-top-info prd-social prd-pull-right">
                    <?php foreach ($company->get('socialnetworks') ? $company->get('socialnetworks') : array() as $soc) : ?>
                        <li><a href="<?php echo $soc['url'] ?>" target="_blank" class="prd-<?php echo $soc['type'] ?>"><i class="fa fa-<?php echo $soc['type'] ?>"></i></a></li>
                    <?php endforeach; ?>
                </ul>
                <div class="prd-top-info prd-pull-right">
                    <small>
                        <i class="fa fa-envelope-o"></i> <?php echo $company->get('email') ?>&nbsp;
                        <i class="fa fa-phone"></i> <?php echo $company->get('telephone') ?>&nbsp;
                        <i class="fa fa-map-marker"></i> <?php echo $company->get('location')['variants']['zip_city_country'] ?>
                    </small>
                </div>
                <div class="prd-clearfix"></div>
                <div id="prd-content" class="prd-row">
                    <div class="col-md-8 col-sm-12">
                        <br> <br> <br> <br>
                        <?php
                        $team_langs = array();
                        ?>
                        <?php foreach ($team->get_members() as $member) : ?>
                            <div class="prd-row prd-team-member">
                                <div class="col-md-4">
                                    <a href="javascript:void(0)">
                                        <img src="<?php echo is_array($member->get('image')) ? $member->get('image')['url'] : $this->get_url() . 'assets/img/placeholder-310x310.png' ?>">
                                    </a>
                                    <br>
                                    <?php if ($member->get('residencelocation')['timezoneoffset']) :
                                        $dt = new DateTime();
                                        $dt->setTimezone(new DateTimeZone($member->get('residencelocation')['timezoneid']));
                                        ?>
                                        <div class="prd-text-big prd-padding-top-10 prd-text-thin"><?php echo $dt->format('h:i a') ?></div>
                                    <?php endif ?>
                                    <ul class="prd-list-unstyled prd-text-thin prd-information-list">
                                        <?php if ($member->get('residencelocation')['addresscomponents']) : ?>
                                            <li class="prd-text-grey text-overflow"><i class="fa fa-map-marker"></i>
                                                <?php echo $member->get('residencelocation')['variants']['city_country'] ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($member->get('phone') ? $member->get('phone') : $member->get('mobile')) : ?>
                                            <li class="text-overflow"><i class="fa fa-phone"></i>
                                                <?php echo $member->get('phone') ? $member->get('phone') : $member->get('mobile') ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($member->get('email')) : ?>
                                            <li class="text-overflow"><i class="fa fa-envelope"></i>
                                                <?php echo $member->get('email') ?>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="col-md-8">
                                    <p class="prd-sub-title prd-underlined">
                                        <?php echo $member->get('name') ?>
                                        <?php if ($team->get('owner') === $member->get('id')) : ?>
                                            <small class="prd-text-grey">- Owner</small>
                                        <?php else : ?>
                                            <small class="prd-text-grey">- Member</small>
                                        <?php endif; ?>
                                    </p>
                                    <p class="prd-ellipsis"><?php echo $member->get('summary') ?></p>
                                    <p class="prd-hide"><?php echo $member->get('summary') ?></p>
                                    <?php if ($member->get('languages')) : ?>
                                        <?php
                                        $spoken = array();
                                        foreach ($member->get('languages') as $lang) {
                                            $spoken[] = $lang['name'];
                                        }

                                        sort($spoken, SORT_ASC);

                                        $team_langs[ implode(' ', $spoken) ][] = $member;
                                        ?>
                                        <div class="prd-speak-language"><span>I speak:</span> <?php echo implode(', ', $spoken) ?></div>
                                        <hr>
                                    <?php endif; ?>
                                    <?php if ($member->get('skills')) : ?>
                                        <?php
                                        $skills = array();
                                        foreach ($member->get('skills') as $skill) {
                                            $skills[] = $skill["name"];
                                            if (count($skills) >= 5) break;
                                        }
                                        ?>
                                        <b>Skills</b><br>
                                        <span class="prd-text-grey prd-text-thin"><?php echo implode(", ", $skills) ?></span>
                                        <hr>
                                    <?php endif; ?>
                                    <?php
                                    $education = "";
                                    $school = "";
                                    if ($member->get('educations')) {
                                        foreach ($member->get('educations') as $tmp_education) {
                                            $educationenddate = DateTime::createFromFormat('U', $tmp_education["enddate"]);
                                            $educationdegree = $tmp_education["degree"];
                                            $educationfieldofstudy = $tmp_education["fieldofstudy"];
                                            $educationschool = $tmp_education["name"];
                                            if ($tmp_education["degree"] && $tmp_education["fieldofstudy"]) {
                                                $education = $tmp_education["degree"] . ", " . $tmp_education["fieldofstudy"];
                                            } else {
                                                $education = $tmp_education["degree"] . $tmp_education["fieldofstudy"];
                                            }
                                            if ($tmp_education["location"]["name"] && $educationenddate->format('Y')) {
                                                $school = $tmp_education["location"]["name"] . "&nbsp;(" . $educationenddate->format('Y') . ")";
                                            } else {
                                                $school = $tmp_education["location"]["name"];
                                            }
                                            break;
                                        }
                                    }
                                    ?>
                                    <?php if ($education || $school) : ?>
                                        <div>
                                            <b>Education</b><br>
                                            <div class="prd-text-grey prd-text-thin"><?php echo $education ?></div>
                                            <div class="prd-text-grey prd-text-thin"><?php echo $school ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                    <div class="col-md-4 col-sm-12">
                        <br> <br> <br> <br>
                        <div>
                            <p class="prd-sub-title prd-underlined">We speak</p>
                            <?php foreach ($team_langs as $key => $mems) : ?>
                                <ul class="prd-we-speak-images prd-list-unstyled">
                                    <li class="<?php echo $key ?>">
                                        <?php foreach ($mems as $mem) : ?>
                                            <img src="<?php echo is_array($mem->get('image')) ? $mem->get('image')['url'] : $this->get_url() . 'assets/img/placeholder-310x310.png' ?>"
                                                 title="<?php echo $mem->get('name') ?>">
                                        <?php endforeach; ?>
                                    </li>

                                </ul>
                                <div class="prd-clearfix"></div>
                                <br>
                                <div class="prd-we-speak-labels prd-no-space">
                                    <?php foreach (explode(' ', $key) as $lang) : ?>
                                        <span class="wespeak-label">
									    <span class="prd-dark-bg prd-speak-language"><?php echo $lang ?></span>
								    </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="prd-clearfix"></div>
                        <br>
                        <br>


                        <p class="prd-sub-title prd-underlined">Industrial experience</p>
                        <?php $randid = md5(rand(0, 65565)) ?>
                        <div id="pos-graph-<?php echo $randid ?>" class="prd-canvas-wrapper">
                            <script type="text/javascript">
                                jQuery(function ($) {
                                    var timer;
                                    $(window).resize(function () {
                                        clearTimeout(timer);
                                        timer = setTimeout(function () {
                                            drawPositionsGraph1("pos-graph-<?php echo $randid ?>", "", <?php echo json_encode($team->get_industries()) ?>, 112, 20);
                                        }, 500);
                                    }).resize()
                                });
                            </script>
                            <canvas id="canvas_pos-graph-<?php echo $randid ?>" height="146" width="347"></canvas>
                        </div>

                        <div class="prd-clearfix"></div>
                        <br>
                        <br>


                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php elseif ($type === Prodii::COMPANY) : ?>
    <?php foreach ($data as $company) : ?>
        <div class="prd-body">
            <div id="prd-breadcrumb" style="font-family: Alegreya Sans;">
                <span class="prd-text-white"><?php echo $company->get('name') ?></span>
            </div>

            <div class="prd-white-bg prd-margin-top-15">
                <div class="prd-header prd-padding-15" style="padding-bottom:0px;">

                    <div class="prd-row">
                        <div class="col-md-7">
                            <img src="<?php echo $company->get('image')['url'] ?>">
                        </div>
                        <div class="col-md-5">
                            <h2><?php echo $company->get('name'); ?></h2>
                            <p><?php echo $company->get('shortdesc') ?></p>
                            <br>
                            <div class="prd-row">
                                <div class="col-md-6 text-overflow">
                                    <i class="fa fa-fw fa-phone prd-text-darkgrey"></i>
                                    <?php echo $company->get('telephone') ?>
                                    <br>
                                    <a href="mailto:<?php echo $company->get('email') ?>">
                                        <i class="fa fa-fw fa-envelope prd-text-darkgrey"></i>
                                        <?php echo $company->get('email') ?>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <ul class="prd-social">
                                        <?php if ($company->get('socialnetworks')) : ?>
                                            <?php foreach ($company->get('socialnetworks') as $soc) : ?>
                                                <li><a href="<?php echo $soc['url'] ?>" target="_blank" class="prd-<?php echo $soc['type'] ?>"><i class="fa fa-<?php echo $soc['type'] ?>"></i></a></li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="prd-row padding-bottom-15 padding-top-15">

                            </div>
                        </div>
                    </div>

                    <div id="map-<?php echo $company->id ?>" data-prodii-map="<?php echo $company->get('location')['latitude'] . ',' . $company->get('location')['longitude'] ?>,14,-200,-40"
                         class="prd-row prd-map" style="position: relative; overflow: hidden;">
                    </div>
                    <div class="prd-map-footer">
                        <?php if ($company->get('location')['timezoneoffset']) :
                            $dt = new DateTime();
                            $dt->setTimezone(new DateTimeZone($company->get('location')['timezoneid']));
                            ?>
                            <i class="fa fa-map-marker"></i>
                            <?php printf('%s Local time (%s GMT %s)', $dt->format('h:i a'), $company->get('location')['formattedaddress'], $dt->format('P')) ?>
                        <?php endif ?>
                    </div>

                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php elseif ($type === Prodii::PROFILE) : ?>
    <?php foreach ($data as $profile) : ?>
        <div class="prd-body">
            <div id="prd-breadcrumb">
                <span class="prd-text-white"><?php echo $profile->get('name') ?></span>
            </div>
            <div class="prd-white-bg prd-margin-top-15">
                <div class="prd-header prd-padding-15" style="padding-bottom:0px;">
                    <div class="prd-row">
                        <div class="col-md-3" style="text-align:center;">
                            <img style="margin-bottom:15px;"
                                 src="<?php echo is_array($profile->get('image')) ? $profile->get('image')['url'] : $this->get_url() . 'assets/img/placeholder-310x310.png' ?>">
                        </div>
                        <div class="col-md-5">
                            <h2><?php echo $profile->get('name') ?></h2>
                            <?php if ($profile->get('positions')) : ?>
                                <?php foreach ($profile->get('positions') as $position) {
                                    if ($position['iscurrent'] === '1') : ?>
                                        <p class="prd-text-thin">Title: <?php echo $position["title"] ?></p>
                                        <p class="prd-text-thin prd-text-big">
                                            My career at <?php echo isset($position["location"]['name']) ? $position["location"]['name'] : '' ?><br>
                                        </p>
                                        <small>
                                            <?php echo $position["title"] ?> Since: <?php echo DateTime::createFromFormat('U', $position['startdate'])->format('jS \o\f F Y') ?>
                                        </small>
                                    <?php endif;
                                }
                                ?>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <div class="text-overflow">
                                <br>
                                <p class="prd-text-thin prd-text-big" style="margin-bottom:0;">Contact me</p>
                                <?php if ($profile->get('email')) : ?>
                                    <a href="mailto:<?php echo $profile->get('email') ?>"><i class="fa fa-fw fa-envelope"></i> <?php echo $profile->get('email') ?></a><br>
                                <?php endif; ?>
                                <?php if ($profile->get('mobile')) : ?>
                                    <a href="tel:<?php echo $profile->get('mobile') ?>"><i class="fa fa-fw fa-phone"></i> <?php echo $profile->get('mobile') ?></a><br>
                                <?php endif; ?>
                                <?php if ($profile->get('phone')) : ?>
                                    <a href="tel:<?php echo $profile->get('phone') ?>"><i class="fa fa-fw fa-phone"></i> <?php echo $profile->get('phone') ?></a><br>
                                <?php endif; ?>
                                <?php if ($profile->get('skype')) : ?>
                                    <span><i class="fa fa-fw fa-skype"></i> <?php echo $profile->get('skype') ?></span><br>
                                <?php endif; ?>
                            </div>

                            <br>
                            <?php if ($profile->get('socialnetworks')) : ?>
                                <p class="prd-text-thin prd-text-big" style="margin-bottom:0;">Follow me</p>
                                <ul class="prd-social">
                                    <?php foreach ($profile->get('socialnetworks') as $network) : ?>
                                        <li>
                                            <a href="<?php echo $network['url'] ?>" class="prd-<?php echo $network['type'] ?>" target="_blank">
                                                <i class="fa fa-<?php echo $network['type'] ?>"></i>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($profile->get('residencelocation')) : ?>
                        <div id="map-<?php echo $profile->id ?>"
                             data-prodii-map="<?php echo $profile->get('residencelocation')['latitude'] . ',' . $profile->get('residencelocation')['longitude'] ?>,14,-200,-40"
                             class="prd-row prd-map" style="position: relative; overflow: hidden;">
                        </div>
                        <div class="prd-map-footer">
                            <?php if ($profile->get('residencelocation')['timezoneoffset']) :
                                $dt = new DateTime();
                                $dt->setTimezone(new DateTimeZone($profile->get('residencelocation')['timezoneid']));
                                ?>
                                <i class="fa fa-map-marker"></i>
                                <?php printf('%s Local time (%s GMT %s)', $dt->format('h:i a'), $profile->get('residencelocation')['variants']['zip_city_country'], $dt->format('P')) ?>
                            <?php endif ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <br>
            <br>
            <br>
            <br>
            <div id="prd-content" class="prd-row">
                <div class="col-md-7">
                    <?php if ($profile->get('summary')) : ?>
                        <p class="prd-sub-title">Bio</p>
                        <p class="prd-ellipsis">
                            <?php echo $profile->get('summary') ?>
                        </p>
                        <p class="prd-hide">
                            <?php echo $profile->get('summary') ?>
                        </p>
                        <div class="prd-clearfix"></div>
                        <br>
                    <?php endif; ?>
                    <?php if ($profile->get('languages')) : ?>
                        <p class="prd-sub-title">I speak</p>
                        <?php foreach ($profile->get('languages') as $language) : ?>
                            <?php
                            $levels = count(Prodii::get_language_levels());
                            $level = $language['level'];

                            $percentage = $levels ? ($levels + 1 - $level) * 100 / $levels : 100 / $levels;
                            ?>
                            <div class="ispeak">
                                <span class="ispeak-name" style="width:<?php echo $percentage ?>%;"><?php echo $language['name'] ?></span>
                                <span class="ispeak-level"><?php echo $language['levellong'] ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="prd-clearfix"></div>
                        <br>
                    <?php endif; ?>

                    <?php if ($profile->get('skills')) : ?>
                        <p class="prd-sub-title">Skills</p>
                        <div class="skills">
                            <?php
                            $levels = Prodii::get_skill_levels();
                            $groups = array();
                            $counter = 0;
                            ?>
                            <?php foreach ($profile->get('skills') as $skill) : ?>
                                <?php $counter++; ?>
                                <?php
                                $groups[ $skill['levelname'] ][] = trim($skill['name']);
                                $yearstext = '';
                                $years = date("Y") - $skill["since"];
                                if ($skill["since"] == 0) {
                                    $yearstext = '&nbsp;';
                                } elseif ($years == 1) {
                                    $yearstext = "1 year";
                                } elseif ($years >= 10) {
                                    $yearstext = "10 years+";
                                } else {
                                    $yearstext = $years . " years";
                                }
                                ?>
                                <div class="skills-column">
                                    <?php foreach (($levels) as $level) : ?>
                                        <?php if ($level['sortorder'] !== $levels[ count($levels) - 1 ]['sortorder']) : ?>
                                            <?php if ($skill['level'] <= $level['sortorder']) : ?>
                                                <?php for ($i = 0; $i < 5; $i++) : ?>
                                                    <span class="<?php echo strtolower($level['name']) ?>"></span>
                                                <?php endfor; ?>
                                            <?php else : ?>
                                                <?php for ($i = 0; $i < 5; $i++) : ?>
                                                    <span class="empty"></span>
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <div class="skills-name text-overflow"><?php echo $skill['name'] ?></div>
                                    <div class="skills-year"><?php echo $yearstext ?></div>
                                </div>
                                <?php if ($counter === 5) : ?>
                                    <div class="skills-column">
                                        <div class="skills-group mastering">
                                            <div></div>
                                            <span class="text-overflow">Mastering</span>
                                        </div>
                                        <div class="skills-group expert">
                                            <div></div>
                                            <span class="text-overflow">Expert</span>
                                        </div>
                                        <div class="skills-group experienced">
                                            <div></div>
                                            <span class="text-overflow">Experienced</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php foreach ($groups as $key => $values) : ?>
                                <div class="skills-bottom">
                                    <span class="skills-label"><?php echo $key ?>:</span>
                                    <span class="skills-content"><?php echo implode(', ', $values) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="prd-clearfix"></div>
                        <br>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 col-md-offset-1">
                    <?php if ($profile->get('residencelocation')) : ?>
                        <div class="timezone">
                            <div class="address"><?php echo $profile->get('residencelocation')['variants']['city_country'] ?></div>
                            <?php if ($profile->get('residencelocation')['timezoneoffset']) : ?>
                                <?php
                                $dt = new DateTime();
                                $dt->setTimezone(new DateTimeZone($profile->get('residencelocation')['timezoneid']));
                                ?>
                                <div class="local"><?php echo sprintf('Local time GMT %s', $dt->format('P')) ?></div>
                                <div>
                                    <span class="time"><?php echo $dt->format('h:i') ?></span>
                                    <span class="ampm"><?php echo $dt->format('a') ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="prd-clearfix"></div>
                        <br>
                    <?php endif; ?>
                    <?php if ($profile->get('socialnetworks')) : ?>
                        <ul class="prd-social prd-list-unstyled">

                            <?php foreach ($profile->get('socialnetworks') as $network) : ?>
                                <li class="clearfix">
                                    <a class="prd-<?php echo $network['type'] ?> pull-left prd-social-icon" target="_blank" href="<?php echo $network['fullurl'] ?>">
                                        <i class="fa fa-<?php echo $network['type'] ?>"></i>
                                    </a>
                                    <div class="overflow-hidden">
                                        <a target="_blank" href="<?php echo $network['fullurl'] ?>"><?php echo $network['url'] ?></a><br>
                                        <span class="text-muted"><?php echo $network['title'] ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if ($profile->get('positions')) : ?>
                        <p class="prd-sub-title prd-underlined">Industrial experience</p>
                        <?php $randid = md5(rand(0, 65565)) ?>
                        <div id="pos-graph-<?php echo $randid ?>" class="prd-canvas-wrapper">
                            <script type="text/javascript">
                                jQuery(function ($) {
                                    var timer;
                                    $(window).resize(function () {
                                        clearTimeout(timer);
                                        timer = setTimeout(function () {
                                            drawPositionsGraph1("pos-graph-<?php echo $randid ?>", "", <?php echo json_encode($profile->get_industries()) ?>, 112, 20);
                                        }, 500);
                                    }).resize()
                                });
                            </script>
                            <canvas id="canvas_pos-graph-<?php echo $randid ?>" height="146" width="347"></canvas>
                        </div>
                        <div class="prd-clearfix"></div>
                        <br>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

