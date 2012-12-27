<?php
/* =========================================连接数据库================================================= */
include_once 'sql/get_information.php';

//session_start();
//$_SESSION['userid'] = 1;

/* =========================================获取user的基本信息================================================= */
//if (!isset($_GET['user_id']))
//    $user_id = 5;
//else
//    $user_id = $_GET['user_id'];
session_start();
$user_id = $_SESSION['userid'];

$currentUser = get_user($user_id);

/* =========================================获取user的兴趣爱好================================================= */
$tags = get_tags($user_id);

$userTag = "";
for ($i = 0; $i < count($tags); $i++) {
    $userTag .= $tags[$i]['user_tag'] . ";";
}

$tagMaxNumber = 10;

/* =========================================获取用户年龄================================================= */
$userAge = get_age($user_id);
?>

<div data-role="page" data-theme="b" data-id="profile_edit_page" data-add-back-btn="true">
    <div data-role="header" data-id="header" data-theme="b" >
        <a id="return_button" href="index.html" data-rel="back">返回</a>
        <h2>搜索</h2>

        <a id="search_confirm" data-role="button" data-icon="forward" class="ui-btn-right">确定</a>

        <script>
            $(document).ready(function(){
                
                $('#search_confirm').click(function(){
                    $.post("search_check.php", $('form#search_form').serialize(), function(data){
                        $('#search_form').hide();
                        $('#search_result').html(data);
                        $('#search_result').listview('refresh');
                    });
                });
                
                $("#return_button").click(function () {
                    $('#search_form').show();
                    $('#search_result').hide();
                })
            });
        </script>
    </div>

    <div data-role="content" data-scroll="true">
        <form id="search_form">
            <ul class="l-detail l-edit l-inset" data-role="listview" data-inset="true">

                <li>
                    <label>性别：</label>
                    <fieldset data-role="controlgroup" data-type="horizontal" class="l-button">
                        <input type="radio" name="gender" id="radio-choice-1" value="男"/>
                        <label for="radio-choice-1">男</label>

                        <input type="radio" name="gender" id="radio-choice-2" value="女"  />
                        <label for="radio-choice-2">女</label>

                    </fieldset>
                </li>

                <li class="l-distance">
                    <label>距离：</label>

                    <fieldset data-role="controlgroup" data-type="horizontal" class="l-button">
                        <input type="radio" name="distance" id="pos-radio-choice-1" value="500" checked="checked"/>
                        <label for="pos-radio-choice-1">< 500m</label>

                        <input type="radio" name="distance" id="pos-radio-choice-2" value="5000"  />
                        <label for="pos-radio-choice-2">< 5km</label>

                        <input type="radio" name="distance" id="pos-radio-choice-3" value="50000"/>
                        <label for="pos-radio-choice-3">< 50km</label>
                    </fieldset>

                </li>

                <li>
                    <label>爱好：</label>
                    <fieldset class="l-taglist">
                        <?php
                        $i = 0;
                        $count = count($tags);
                        for (; $i < $count; $i++) {
                            ?>
                            <input type="search" name="tag<?php echo $i; ?>" data-tag-id="<?php echo $i; ?>" value="<?php echo trim($tags[$i]['user_tag']); ?>"/>
                            <?php
                        }
                        ?>
                        <?php
                        for (; $i < $tagMaxNumber; $i++) {
                            ?>
                            <input type="search" name="tag<?php echo $i; ?>" data-tag-id="<?php echo $i ?>" value="" hidden/>
                            <?php
                        }
                        ?>
                        <input id="new_tag" type="search" data-tag-count="<?php echo $count; ?>" data-tag-max-number="<?php echo $tagMaxNumber; ?>" value="new_tag"/>
                    </fieldset>

                </li>



                <li class="l-select-age">
                    <label>年龄：</label>

                    <div data-role="fieldcontain">
                        <select name="select-choice-1" id="select-choice-1" data-inline="true"  class="l-select">
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                            <option value="26">26</option>
                            <option value="27">27</option>
                            <option value="28">28</option>
                            <option value="29">29</option>
                            <option value="30">30</option>
                            <option value="31">31</option>
                            <option value="32">32</option>
                            <option value="33">33</option>
                            <option value="34">34</option>
                            <option value="35">35</option>
                            <option value="36">36</option>
                            <option value="37">37</option>
                            <option value="38">38</option>
                            <option value="39">39</option>
                            <option value="40">40</option>
                            <option value="41">41</option>
                            <option value="42">42</option>
                            <option value="43">43</option>
                            <option value="44">44</option>
                            <option value="45">45</option>
                            <option value="46">46</option>
                            <option value="47">47</option>
                            <option value="48">48</option>
                            <option value="49">49</option>
                            <option value="50">50</option>
                            <option value="51">51</option>
                            <option value="52">52</option>
                            <option value="53">53</option>
                            <option value="54">54</option>
                            <option value="55">55</option>
                            <option value="56">56</option>
                            <option value="57">57</option>
                            <option value="58">58</option>
                            <option value="59">59</option>
                            <option value="60">60</option>
                            <option value="61">61</option>
                            <option value="62">62</option>
                            <option value="63">63</option>
                            <option value="64">64</option>
                            <option value="65">65</option>
                            <option value="66">66</option>
                            <option value="67">67</option>
                            <option value="68">68</option>
                            <option value="69">69</option>
                            <option value="70">70</option>
                            <option value="71">71</option>
                            <option value="72">72</option>
                            <option value="73">73</option>
                            <option value="74">74</option>
                            <option value="75">75</option>
                            <option value="76">76</option>
                            <option value="77">77</option>
                            <option value="78">78</option>
                            <option value="79">79</option>
                            <option value="80">80</option>
                        </select>
                        <select name="select-choice-2" id="select-choice-2" data-inline="true" class="l-select">
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                            <option value="26">26</option>
                            <option value="27">27</option>
                            <option value="28">28</option>
                            <option value="29">29</option>
                            <option value="30">30</option>
                            <option value="31">31</option>
                            <option value="32">32</option>
                            <option value="33">33</option>
                            <option value="34">34</option>
                            <option value="35">35</option>
                            <option value="36">36</option>
                            <option value="37">37</option>
                            <option value="38">38</option>
                            <option value="39">39</option>
                            <option value="40">40</option>
                            <option value="41">41</option>
                            <option value="42">42</option>
                            <option value="43">43</option>
                            <option value="44">44</option>
                            <option value="45">45</option>
                            <option value="46">46</option>
                            <option value="47">47</option>
                            <option value="48">48</option>
                            <option value="49">49</option>
                            <option value="50">50</option>
                            <option value="51">51</option>
                            <option value="52">52</option>
                            <option value="53">53</option>
                            <option value="54">54</option>
                            <option value="55">55</option>
                            <option value="56">56</option>
                            <option value="57">57</option>
                            <option value="58">58</option>
                            <option value="59">59</option>
                            <option value="60">60</option>
                            <option value="61">61</option>
                            <option value="62">62</option>
                            <option value="63">63</option>
                            <option value="64">64</option>
                            <option value="65">65</option>
                            <option value="66">66</option>
                            <option value="67">67</option>
                            <option value="68">68</option>
                            <option value="69">69</option>
                            <option value="70">70</option>
                            <option value="71">71</option>
                            <option value="72">72</option>
                            <option value="73">73</option>
                            <option value="74">74</option>
                            <option value="75">75</option>
                            <option value="76">76</option>
                            <option value="77">77</option>
                            <option value="78">78</option>
                            <option value="79">79</option>
                            <option value="80">80</option>
                        </select>
                    </div>

                </li>
            </ul>
        </form>
        <div >
            <ul id="search_result" class="l-inset" data-role="listview" data-inset="true">

            </ul>
        </div>


    </div>

    <div data-role="footer" data-theme="b" data-id="footer">
        <h2 >Team Luff</h2>
    </div>

</div>
<!--    </body>
</html>-->
