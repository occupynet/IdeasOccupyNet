
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
          "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>openid.php - lightopenid in LightOpenID - Gitorious</title>
<link href="/stylesheets/gts-common.css?1317147383" media="screen" rel="stylesheet" type="text/css" />
<link href="//fonts.googleapis.com/css?family=Nobile&v1" type="text/css" rel="stylesheet">

<script src="/javascripts/all.js?1317147383" type="text/javascript"></script>      <link href="/stylesheets/prettify/prettify.css?1317147383" media="screen" rel="stylesheet" type="text/css" />    <script src="/javascripts/lib/prettify.js?1317147383" type="text/javascript"></script>        <script type="text/javascript" charset="utf-8">
      $(document).ready(function(){
          if ($("#codeblob tr td.line-numbers:last").text().length < 3500) {
              prettyPrint();
          } else {
              $("#long-file").show().find("a#highlight-anyway").click(function(e){
                  prettyPrint();
                  e.preventDefault();
              });
          }
      });
    </script>
  <!--[if IE 8]><link rel="stylesheet" href="/stylesheets/ie8.css" type="text/css"><![endif]-->
<!--[if IE 7]><link rel="stylesheet" href="/stylesheets/ie7.css" type="text/css"><![endif]-->
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-52238-3']);
_gaq.push(['_setDomainName', '.gitorious.org'])
_gaq.push(['_trackPageview']);
(function() {
   var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
   ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
   (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
})();
</script>
</head>
<body id="blobs">
  <div id="wrapper">
        <ul id="user-nav">
      <li><a href="/">Dashboard</a></li>
      
    	<li class="secondary"><a href="/users/new">Register</a></li>
    <li class="secondary"><a href="/login">Login</a></li>
    </ul>
    <div id="header">
      <h1 id="logo">
        <a href="/"><img alt="Logo" src="/img/logo.png?1294322727" /></a>
      </h1>
      <ul id="menu">
                  <li class="activity"><a href="/activities">Activities</a></li>
          <li class="projects"><a href="/projects">Projects</a></li>
          <li class="teams"><a href="/teams">Teams</a></li>
              </ul>
    </div>
    <div id="top-bar">
      <ul id="breadcrumbs">
        <li class="project"><a href="/lightopenid">LightOpenID</a></li><li class="repository"><a href="/lightopenid/lightopenid">lightopenid</a></li><li class="branch"><a href="/lightopenid/lightopenid/commits/master">master</a></li><li class="tree"><a href="/lightopenid/lightopenid/trees/master">/</a></li><li class="file"><a href="/lightopenid/lightopenid/blobs/master/openid.php">openid.php</a></li>      </ul>
              <div id="searchbox">
          


<div class="search_bar">
<form action="http://gitorious.org/search" method="get"><p>
  <input class="text search-field round-5" id="q" name="q" type="text" /> 
  <input type="submit" value="Search" class="search-submit round-5" />
</p>  
<p class="hint search-hint" style="display: none;">
  eg. 'wrapper', 'category:python' or '"document database"'
  </p>
</form></div>
        </div>
          </div>
    <div id="container" class="">
      <div id="content" class="">
        
        



<div class="page-meta">
  <ul class="page-actions">
    <li>Blob contents</li>
    <li><a href="/lightopenid/lightopenid/blobs/history/master/openid.php">Blob history</a></li>
    <li><a href="/lightopenid/lightopenid/blobs/raw/master/openid.php">Raw blob data</a></li>
  </ul>
</div>


<!-- mime: application/httpd-php -->

       <div id="long-file" style="display:none"
                  class="help-box center error round-5">
               <div class="icon error"></div>        <p>
          This file looks large and may slow your browser down if we attempt
          to syntax highlight it, so we are showing it without any
          pretty colors.
          <a href="#highlight-anyway" id="highlight-anyway">Highlight
          it anyway</a>.
        </p>
     </div>    <table id="codeblob" class="highlighted lang-php">
<tr id="line1">
<td class="line-numbers"><a href="#line1" name="line1">1</a></td>
<td class="code"><pre class="prettyprint lang-php">&lt;?php</pre></td>
</tr>
<tr id="line2">
<td class="line-numbers"><a href="#line2" name="line2">2</a></td>
<td class="code"><pre class="prettyprint lang-php">/**</pre></td>
</tr>
<tr id="line3">
<td class="line-numbers"><a href="#line3" name="line3">3</a></td>
<td class="code"><pre class="prettyprint lang-php"> * This class provides a simple interface for OpenID (1.1 and 2.0) authentication.</pre></td>
</tr>
<tr id="line4">
<td class="line-numbers"><a href="#line4" name="line4">4</a></td>
<td class="code"><pre class="prettyprint lang-php"> * Supports Yadis discovery.</pre></td>
</tr>
<tr id="line5">
<td class="line-numbers"><a href="#line5" name="line5">5</a></td>
<td class="code"><pre class="prettyprint lang-php"> * The authentication process is stateless/dumb.</pre></td>
</tr>
<tr id="line6">
<td class="line-numbers"><a href="#line6" name="line6">6</a></td>
<td class="code"><pre class="prettyprint lang-php"> *</pre></td>
</tr>
<tr id="line7">
<td class="line-numbers"><a href="#line7" name="line7">7</a></td>
<td class="code"><pre class="prettyprint lang-php"> * Usage:</pre></td>
</tr>
<tr id="line8">
<td class="line-numbers"><a href="#line8" name="line8">8</a></td>
<td class="code"><pre class="prettyprint lang-php"> * Sign-on with OpenID is a two step process:</pre></td>
</tr>
<tr id="line9">
<td class="line-numbers"><a href="#line9" name="line9">9</a></td>
<td class="code"><pre class="prettyprint lang-php"> * Step one is authentication with the provider:</pre></td>
</tr>
<tr id="line10">
<td class="line-numbers"><a href="#line10" name="line10">10</a></td>
<td class="code"><pre class="prettyprint lang-php"> * &lt;code&gt;</pre></td>
</tr>
<tr id="line11">
<td class="line-numbers"><a href="#line11" name="line11">11</a></td>
<td class="code"><pre class="prettyprint lang-php"> * $openid = new LightOpenID('my-host.example.org');</pre></td>
</tr>
<tr id="line12">
<td class="line-numbers"><a href="#line12" name="line12">12</a></td>
<td class="code"><pre class="prettyprint lang-php"> * $openid-&gt;identity = 'ID supplied by user';</pre></td>
</tr>
<tr id="line13">
<td class="line-numbers"><a href="#line13" name="line13">13</a></td>
<td class="code"><pre class="prettyprint lang-php"> * header('Location: ' . $openid-&gt;authUrl());</pre></td>
</tr>
<tr id="line14">
<td class="line-numbers"><a href="#line14" name="line14">14</a></td>
<td class="code"><pre class="prettyprint lang-php"> * &lt;/code&gt;</pre></td>
</tr>
<tr id="line15">
<td class="line-numbers"><a href="#line15" name="line15">15</a></td>
<td class="code"><pre class="prettyprint lang-php"> * The provider then sends various parameters via GET, one of them is openid_mode.</pre></td>
</tr>
<tr id="line16">
<td class="line-numbers"><a href="#line16" name="line16">16</a></td>
<td class="code"><pre class="prettyprint lang-php"> * Step two is verification:</pre></td>
</tr>
<tr id="line17">
<td class="line-numbers"><a href="#line17" name="line17">17</a></td>
<td class="code"><pre class="prettyprint lang-php"> * &lt;code&gt;</pre></td>
</tr>
<tr id="line18">
<td class="line-numbers"><a href="#line18" name="line18">18</a></td>
<td class="code"><pre class="prettyprint lang-php"> * if ($this-&gt;data['openid_mode']) {</pre></td>
</tr>
<tr id="line19">
<td class="line-numbers"><a href="#line19" name="line19">19</a></td>
<td class="code"><pre class="prettyprint lang-php"> *     $openid = new LightOpenID('my-host.example.org');</pre></td>
</tr>
<tr id="line20">
<td class="line-numbers"><a href="#line20" name="line20">20</a></td>
<td class="code"><pre class="prettyprint lang-php"> *     echo $openid-&gt;validate() ? 'Logged in.' : 'Failed';</pre></td>
</tr>
<tr id="line21">
<td class="line-numbers"><a href="#line21" name="line21">21</a></td>
<td class="code"><pre class="prettyprint lang-php"> * }</pre></td>
</tr>
<tr id="line22">
<td class="line-numbers"><a href="#line22" name="line22">22</a></td>
<td class="code"><pre class="prettyprint lang-php"> * &lt;/code&gt;</pre></td>
</tr>
<tr id="line23">
<td class="line-numbers"><a href="#line23" name="line23">23</a></td>
<td class="code"><pre class="prettyprint lang-php"> *</pre></td>
</tr>
<tr id="line24">
<td class="line-numbers"><a href="#line24" name="line24">24</a></td>
<td class="code"><pre class="prettyprint lang-php"> * Change the 'my-host.example.org' to your domain name. Do NOT use $_SERVER['HTTP_HOST']</pre></td>
</tr>
<tr id="line25">
<td class="line-numbers"><a href="#line25" name="line25">25</a></td>
<td class="code"><pre class="prettyprint lang-php"> * for that, unless you know what you are doing.</pre></td>
</tr>
<tr id="line26">
<td class="line-numbers"><a href="#line26" name="line26">26</a></td>
<td class="code"><pre class="prettyprint lang-php"> *</pre></td>
</tr>
<tr id="line27">
<td class="line-numbers"><a href="#line27" name="line27">27</a></td>
<td class="code"><pre class="prettyprint lang-php"> * Optionally, you can set $returnUrl and $realm (or $trustRoot, which is an alias).</pre></td>
</tr>
<tr id="line28">
<td class="line-numbers"><a href="#line28" name="line28">28</a></td>
<td class="code"><pre class="prettyprint lang-php"> * The default values for those are:</pre></td>
</tr>
<tr id="line29">
<td class="line-numbers"><a href="#line29" name="line29">29</a></td>
<td class="code"><pre class="prettyprint lang-php"> * $openid-&gt;realm     = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];</pre></td>
</tr>
<tr id="line30">
<td class="line-numbers"><a href="#line30" name="line30">30</a></td>
<td class="code"><pre class="prettyprint lang-php"> * $openid-&gt;returnUrl = $openid-&gt;realm . $_SERVER['REQUEST_URI'];</pre></td>
</tr>
<tr id="line31">
<td class="line-numbers"><a href="#line31" name="line31">31</a></td>
<td class="code"><pre class="prettyprint lang-php"> * If you don't know their meaning, refer to any openid tutorial, or specification. Or just guess.</pre></td>
</tr>
<tr id="line32">
<td class="line-numbers"><a href="#line32" name="line32">32</a></td>
<td class="code"><pre class="prettyprint lang-php"> *</pre></td>
</tr>
<tr id="line33">
<td class="line-numbers"><a href="#line33" name="line33">33</a></td>
<td class="code"><pre class="prettyprint lang-php"> * AX and SREG extensions are supported.</pre></td>
</tr>
<tr id="line34">
<td class="line-numbers"><a href="#line34" name="line34">34</a></td>
<td class="code"><pre class="prettyprint lang-php"> * To use them, specify $openid-&gt;required and/or $openid-&gt;optional before calling $openid-&gt;authUrl().</pre></td>
</tr>
<tr id="line35">
<td class="line-numbers"><a href="#line35" name="line35">35</a></td>
<td class="code"><pre class="prettyprint lang-php"> * These are arrays, with values being AX schema paths (the 'path' part of the URL).</pre></td>
</tr>
<tr id="line36">
<td class="line-numbers"><a href="#line36" name="line36">36</a></td>
<td class="code"><pre class="prettyprint lang-php"> * For example:</pre></td>
</tr>
<tr id="line37">
<td class="line-numbers"><a href="#line37" name="line37">37</a></td>
<td class="code"><pre class="prettyprint lang-php"> *   $openid-&gt;required = array('namePerson/friendly', 'contact/email');</pre></td>
</tr>
<tr id="line38">
<td class="line-numbers"><a href="#line38" name="line38">38</a></td>
<td class="code"><pre class="prettyprint lang-php"> *   $openid-&gt;optional = array('namePerson/first');</pre></td>
</tr>
<tr id="line39">
<td class="line-numbers"><a href="#line39" name="line39">39</a></td>
<td class="code"><pre class="prettyprint lang-php"> * If the server supports only SREG or OpenID 1.1, these are automaticaly</pre></td>
</tr>
<tr id="line40">
<td class="line-numbers"><a href="#line40" name="line40">40</a></td>
<td class="code"><pre class="prettyprint lang-php"> * mapped to SREG names, so that user doesn't have to know anything about the server.</pre></td>
</tr>
<tr id="line41">
<td class="line-numbers"><a href="#line41" name="line41">41</a></td>
<td class="code"><pre class="prettyprint lang-php"> *</pre></td>
</tr>
<tr id="line42">
<td class="line-numbers"><a href="#line42" name="line42">42</a></td>
<td class="code"><pre class="prettyprint lang-php"> * To get the values, use $openid-&gt;getAttributes().</pre></td>
</tr>
<tr id="line43">
<td class="line-numbers"><a href="#line43" name="line43">43</a></td>
<td class="code"><pre class="prettyprint lang-php"> *</pre></td>
</tr>
<tr id="line44">
<td class="line-numbers"><a href="#line44" name="line44">44</a></td>
<td class="code"><pre class="prettyprint lang-php"> *</pre></td>
</tr>
<tr id="line45">
<td class="line-numbers"><a href="#line45" name="line45">45</a></td>
<td class="code"><pre class="prettyprint lang-php"> * The library requires PHP &gt;= 5.1.2 with curl or http/https stream wrappers enabled.</pre></td>
</tr>
<tr id="line46">
<td class="line-numbers"><a href="#line46" name="line46">46</a></td>
<td class="code"><pre class="prettyprint lang-php"> * @author Mewp</pre></td>
</tr>
<tr id="line47">
<td class="line-numbers"><a href="#line47" name="line47">47</a></td>
<td class="code"><pre class="prettyprint lang-php"> * @copyright Copyright (c) 2010, Mewp</pre></td>
</tr>
<tr id="line48">
<td class="line-numbers"><a href="#line48" name="line48">48</a></td>
<td class="code"><pre class="prettyprint lang-php"> * @license http://www.opensource.org/licenses/mit-license.php MIT</pre></td>
</tr>
<tr id="line49">
<td class="line-numbers"><a href="#line49" name="line49">49</a></td>
<td class="code"><pre class="prettyprint lang-php"> */</pre></td>
</tr>
<tr id="line50">
<td class="line-numbers"><a href="#line50" name="line50">50</a></td>
<td class="code"><pre class="prettyprint lang-php">class LightOpenID</pre></td>
</tr>
<tr id="line51">
<td class="line-numbers"><a href="#line51" name="line51">51</a></td>
<td class="code"><pre class="prettyprint lang-php">{</pre></td>
</tr>
<tr id="line52">
<td class="line-numbers"><a href="#line52" name="line52">52</a></td>
<td class="code"><pre class="prettyprint lang-php">    public $returnUrl</pre></td>
</tr>
<tr id="line53">
<td class="line-numbers"><a href="#line53" name="line53">53</a></td>
<td class="code"><pre class="prettyprint lang-php">         , $required = array()</pre></td>
</tr>
<tr id="line54">
<td class="line-numbers"><a href="#line54" name="line54">54</a></td>
<td class="code"><pre class="prettyprint lang-php">         , $optional = array()</pre></td>
</tr>
<tr id="line55">
<td class="line-numbers"><a href="#line55" name="line55">55</a></td>
<td class="code"><pre class="prettyprint lang-php">         , $verify_peer = null</pre></td>
</tr>
<tr id="line56">
<td class="line-numbers"><a href="#line56" name="line56">56</a></td>
<td class="code"><pre class="prettyprint lang-php">         , $capath = null</pre></td>
</tr>
<tr id="line57">
<td class="line-numbers"><a href="#line57" name="line57">57</a></td>
<td class="code"><pre class="prettyprint lang-php">         , $cainfo = null</pre></td>
</tr>
<tr id="line58">
<td class="line-numbers"><a href="#line58" name="line58">58</a></td>
<td class="code"><pre class="prettyprint lang-php">         , $data;</pre></td>
</tr>
<tr id="line59">
<td class="line-numbers"><a href="#line59" name="line59">59</a></td>
<td class="code"><pre class="prettyprint lang-php">    private $identity, $claimed_id;</pre></td>
</tr>
<tr id="line60">
<td class="line-numbers"><a href="#line60" name="line60">60</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected $server, $version, $trustRoot, $aliases, $identifier_select = false</pre></td>
</tr>
<tr id="line61">
<td class="line-numbers"><a href="#line61" name="line61">61</a></td>
<td class="code"><pre class="prettyprint lang-php">            , $ax = false, $sreg = false, $setup_url = null;</pre></td>
</tr>
<tr id="line62">
<td class="line-numbers"><a href="#line62" name="line62">62</a></td>
<td class="code"><pre class="prettyprint lang-php">    static protected $ax_to_sreg = array(</pre></td>
</tr>
<tr id="line63">
<td class="line-numbers"><a href="#line63" name="line63">63</a></td>
<td class="code"><pre class="prettyprint lang-php">        'namePerson/friendly'     =&gt; 'nickname',</pre></td>
</tr>
<tr id="line64">
<td class="line-numbers"><a href="#line64" name="line64">64</a></td>
<td class="code"><pre class="prettyprint lang-php">        'contact/email'           =&gt; 'email',</pre></td>
</tr>
<tr id="line65">
<td class="line-numbers"><a href="#line65" name="line65">65</a></td>
<td class="code"><pre class="prettyprint lang-php">        'namePerson'              =&gt; 'fullname',</pre></td>
</tr>
<tr id="line66">
<td class="line-numbers"><a href="#line66" name="line66">66</a></td>
<td class="code"><pre class="prettyprint lang-php">        'birthDate'               =&gt; 'dob',</pre></td>
</tr>
<tr id="line67">
<td class="line-numbers"><a href="#line67" name="line67">67</a></td>
<td class="code"><pre class="prettyprint lang-php">        'person/gender'           =&gt; 'gender',</pre></td>
</tr>
<tr id="line68">
<td class="line-numbers"><a href="#line68" name="line68">68</a></td>
<td class="code"><pre class="prettyprint lang-php">        'contact/postalCode/home' =&gt; 'postcode',</pre></td>
</tr>
<tr id="line69">
<td class="line-numbers"><a href="#line69" name="line69">69</a></td>
<td class="code"><pre class="prettyprint lang-php">        'contact/country/home'    =&gt; 'country',</pre></td>
</tr>
<tr id="line70">
<td class="line-numbers"><a href="#line70" name="line70">70</a></td>
<td class="code"><pre class="prettyprint lang-php">        'pref/language'           =&gt; 'language',</pre></td>
</tr>
<tr id="line71">
<td class="line-numbers"><a href="#line71" name="line71">71</a></td>
<td class="code"><pre class="prettyprint lang-php">        'pref/timezone'           =&gt; 'timezone',</pre></td>
</tr>
<tr id="line72">
<td class="line-numbers"><a href="#line72" name="line72">72</a></td>
<td class="code"><pre class="prettyprint lang-php">        );</pre></td>
</tr>
<tr id="line73">
<td class="line-numbers"><a href="#line73" name="line73">73</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line74">
<td class="line-numbers"><a href="#line74" name="line74">74</a></td>
<td class="code"><pre class="prettyprint lang-php">    function __construct($host)</pre></td>
</tr>
<tr id="line75">
<td class="line-numbers"><a href="#line75" name="line75">75</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line76">
<td class="line-numbers"><a href="#line76" name="line76">76</a></td>
<td class="code"><pre class="prettyprint lang-php">        $this-&gt;trustRoot = (strpos($host, '://') ? $host : 'http://' . $host);</pre></td>
</tr>
<tr id="line77">
<td class="line-numbers"><a href="#line77" name="line77">77</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ((!empty($_SERVER['HTTPS']) &amp;&amp; $_SERVER['HTTPS'] != 'off')</pre></td>
</tr>
<tr id="line78">
<td class="line-numbers"><a href="#line78" name="line78">78</a></td>
<td class="code"><pre class="prettyprint lang-php">            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])</pre></td>
</tr>
<tr id="line79">
<td class="line-numbers"><a href="#line79" name="line79">79</a></td>
<td class="code"><pre class="prettyprint lang-php">            &amp;&amp; $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')</pre></td>
</tr>
<tr id="line80">
<td class="line-numbers"><a href="#line80" name="line80">80</a></td>
<td class="code"><pre class="prettyprint lang-php">        ) {</pre></td>
</tr>
<tr id="line81">
<td class="line-numbers"><a href="#line81" name="line81">81</a></td>
<td class="code"><pre class="prettyprint lang-php">            $this-&gt;trustRoot = (strpos($host, '://') ? $host : 'https://' . $host);</pre></td>
</tr>
<tr id="line82">
<td class="line-numbers"><a href="#line82" name="line82">82</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line83">
<td class="line-numbers"><a href="#line83" name="line83">83</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line84">
<td class="line-numbers"><a href="#line84" name="line84">84</a></td>
<td class="code"><pre class="prettyprint lang-php">        if(($host_end = strpos($this-&gt;trustRoot, '/', 8)) !== false) {</pre></td>
</tr>
<tr id="line85">
<td class="line-numbers"><a href="#line85" name="line85">85</a></td>
<td class="code"><pre class="prettyprint lang-php">            $this-&gt;trustRoot = substr($this-&gt;trustRoot, 0, $host_end);</pre></td>
</tr>
<tr id="line86">
<td class="line-numbers"><a href="#line86" name="line86">86</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line87">
<td class="line-numbers"><a href="#line87" name="line87">87</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line88">
<td class="line-numbers"><a href="#line88" name="line88">88</a></td>
<td class="code"><pre class="prettyprint lang-php">        $uri = rtrim(preg_replace('#((?&lt;=\?)|&amp;)openid\.[^&amp;]+#', '', $_SERVER['REQUEST_URI']), '?');</pre></td>
</tr>
<tr id="line89">
<td class="line-numbers"><a href="#line89" name="line89">89</a></td>
<td class="code"><pre class="prettyprint lang-php">        $this-&gt;returnUrl = $this-&gt;trustRoot . $uri;</pre></td>
</tr>
<tr id="line90">
<td class="line-numbers"><a href="#line90" name="line90">90</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line91">
<td class="line-numbers"><a href="#line91" name="line91">91</a></td>
<td class="code"><pre class="prettyprint lang-php">        $this-&gt;data = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $_GET;</pre></td>
</tr>
<tr id="line92">
<td class="line-numbers"><a href="#line92" name="line92">92</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line93">
<td class="line-numbers"><a href="#line93" name="line93">93</a></td>
<td class="code"><pre class="prettyprint lang-php">        if(!function_exists('curl_init') &amp;&amp; !in_array('https', stream_get_wrappers())) {</pre></td>
</tr>
<tr id="line94">
<td class="line-numbers"><a href="#line94" name="line94">94</a></td>
<td class="code"><pre class="prettyprint lang-php">            throw new ErrorException('You must have either https wrappers or curl enabled.');</pre></td>
</tr>
<tr id="line95">
<td class="line-numbers"><a href="#line95" name="line95">95</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line96">
<td class="line-numbers"><a href="#line96" name="line96">96</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line97">
<td class="line-numbers"><a href="#line97" name="line97">97</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line98">
<td class="line-numbers"><a href="#line98" name="line98">98</a></td>
<td class="code"><pre class="prettyprint lang-php">    function __set($name, $value)</pre></td>
</tr>
<tr id="line99">
<td class="line-numbers"><a href="#line99" name="line99">99</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line100">
<td class="line-numbers"><a href="#line100" name="line100">100</a></td>
<td class="code"><pre class="prettyprint lang-php">        switch ($name) {</pre></td>
</tr>
<tr id="line101">
<td class="line-numbers"><a href="#line101" name="line101">101</a></td>
<td class="code"><pre class="prettyprint lang-php">        case 'identity':</pre></td>
</tr>
<tr id="line102">
<td class="line-numbers"><a href="#line102" name="line102">102</a></td>
<td class="code"><pre class="prettyprint lang-php">            if (strlen($value = trim((String) $value))) {</pre></td>
</tr>
<tr id="line103">
<td class="line-numbers"><a href="#line103" name="line103">103</a></td>
<td class="code"><pre class="prettyprint lang-php">                if (preg_match('#^xri:/*#i', $value, $m)) {</pre></td>
</tr>
<tr id="line104">
<td class="line-numbers"><a href="#line104" name="line104">104</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $value = substr($value, strlen($m[0]));</pre></td>
</tr>
<tr id="line105">
<td class="line-numbers"><a href="#line105" name="line105">105</a></td>
<td class="code"><pre class="prettyprint lang-php">                } elseif (!preg_match('/^(?:[=@+\$!\(]|https?:)/i', $value)) {</pre></td>
</tr>
<tr id="line106">
<td class="line-numbers"><a href="#line106" name="line106">106</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $value = &quot;http://$value&quot;;</pre></td>
</tr>
<tr id="line107">
<td class="line-numbers"><a href="#line107" name="line107">107</a></td>
<td class="code"><pre class="prettyprint lang-php">                }</pre></td>
</tr>
<tr id="line108">
<td class="line-numbers"><a href="#line108" name="line108">108</a></td>
<td class="code"><pre class="prettyprint lang-php">                if (preg_match('#^https?://[^/]+$#i', $value, $m)) {</pre></td>
</tr>
<tr id="line109">
<td class="line-numbers"><a href="#line109" name="line109">109</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $value .= '/';</pre></td>
</tr>
<tr id="line110">
<td class="line-numbers"><a href="#line110" name="line110">110</a></td>
<td class="code"><pre class="prettyprint lang-php">                }</pre></td>
</tr>
<tr id="line111">
<td class="line-numbers"><a href="#line111" name="line111">111</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line112">
<td class="line-numbers"><a href="#line112" name="line112">112</a></td>
<td class="code"><pre class="prettyprint lang-php">            $this-&gt;$name = $this-&gt;claimed_id = $value;</pre></td>
</tr>
<tr id="line113">
<td class="line-numbers"><a href="#line113" name="line113">113</a></td>
<td class="code"><pre class="prettyprint lang-php">            break;</pre></td>
</tr>
<tr id="line114">
<td class="line-numbers"><a href="#line114" name="line114">114</a></td>
<td class="code"><pre class="prettyprint lang-php">        case 'trustRoot':</pre></td>
</tr>
<tr id="line115">
<td class="line-numbers"><a href="#line115" name="line115">115</a></td>
<td class="code"><pre class="prettyprint lang-php">        case 'realm':</pre></td>
</tr>
<tr id="line116">
<td class="line-numbers"><a href="#line116" name="line116">116</a></td>
<td class="code"><pre class="prettyprint lang-php">            $this-&gt;trustRoot = trim($value);</pre></td>
</tr>
<tr id="line117">
<td class="line-numbers"><a href="#line117" name="line117">117</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line118">
<td class="line-numbers"><a href="#line118" name="line118">118</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line119">
<td class="line-numbers"><a href="#line119" name="line119">119</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line120">
<td class="line-numbers"><a href="#line120" name="line120">120</a></td>
<td class="code"><pre class="prettyprint lang-php">    function __get($name)</pre></td>
</tr>
<tr id="line121">
<td class="line-numbers"><a href="#line121" name="line121">121</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line122">
<td class="line-numbers"><a href="#line122" name="line122">122</a></td>
<td class="code"><pre class="prettyprint lang-php">        switch ($name) {</pre></td>
</tr>
<tr id="line123">
<td class="line-numbers"><a href="#line123" name="line123">123</a></td>
<td class="code"><pre class="prettyprint lang-php">        case 'identity':</pre></td>
</tr>
<tr id="line124">
<td class="line-numbers"><a href="#line124" name="line124">124</a></td>
<td class="code"><pre class="prettyprint lang-php">            # We return claimed_id instead of identity,</pre></td>
</tr>
<tr id="line125">
<td class="line-numbers"><a href="#line125" name="line125">125</a></td>
<td class="code"><pre class="prettyprint lang-php">            # because the developer should see the claimed identifier,</pre></td>
</tr>
<tr id="line126">
<td class="line-numbers"><a href="#line126" name="line126">126</a></td>
<td class="code"><pre class="prettyprint lang-php">            # i.e. what he set as identity, not the op-local identifier (which is what we verify)</pre></td>
</tr>
<tr id="line127">
<td class="line-numbers"><a href="#line127" name="line127">127</a></td>
<td class="code"><pre class="prettyprint lang-php">            return $this-&gt;claimed_id;</pre></td>
</tr>
<tr id="line128">
<td class="line-numbers"><a href="#line128" name="line128">128</a></td>
<td class="code"><pre class="prettyprint lang-php">        case 'trustRoot':</pre></td>
</tr>
<tr id="line129">
<td class="line-numbers"><a href="#line129" name="line129">129</a></td>
<td class="code"><pre class="prettyprint lang-php">        case 'realm':</pre></td>
</tr>
<tr id="line130">
<td class="line-numbers"><a href="#line130" name="line130">130</a></td>
<td class="code"><pre class="prettyprint lang-php">            return $this-&gt;trustRoot;</pre></td>
</tr>
<tr id="line131">
<td class="line-numbers"><a href="#line131" name="line131">131</a></td>
<td class="code"><pre class="prettyprint lang-php">        case 'mode':</pre></td>
</tr>
<tr id="line132">
<td class="line-numbers"><a href="#line132" name="line132">132</a></td>
<td class="code"><pre class="prettyprint lang-php">            return empty($this-&gt;data['openid_mode']) ? null : $this-&gt;data['openid_mode'];</pre></td>
</tr>
<tr id="line133">
<td class="line-numbers"><a href="#line133" name="line133">133</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line134">
<td class="line-numbers"><a href="#line134" name="line134">134</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line135">
<td class="line-numbers"><a href="#line135" name="line135">135</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line136">
<td class="line-numbers"><a href="#line136" name="line136">136</a></td>
<td class="code"><pre class="prettyprint lang-php">    /**</pre></td>
</tr>
<tr id="line137">
<td class="line-numbers"><a href="#line137" name="line137">137</a></td>
<td class="code"><pre class="prettyprint lang-php">     * Checks if the server specified in the url exists.</pre></td>
</tr>
<tr id="line138">
<td class="line-numbers"><a href="#line138" name="line138">138</a></td>
<td class="code"><pre class="prettyprint lang-php">     *</pre></td>
</tr>
<tr id="line139">
<td class="line-numbers"><a href="#line139" name="line139">139</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @param $url url to check</pre></td>
</tr>
<tr id="line140">
<td class="line-numbers"><a href="#line140" name="line140">140</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @return true, if the server exists; false otherwise</pre></td>
</tr>
<tr id="line141">
<td class="line-numbers"><a href="#line141" name="line141">141</a></td>
<td class="code"><pre class="prettyprint lang-php">     */</pre></td>
</tr>
<tr id="line142">
<td class="line-numbers"><a href="#line142" name="line142">142</a></td>
<td class="code"><pre class="prettyprint lang-php">    function hostExists($url)</pre></td>
</tr>
<tr id="line143">
<td class="line-numbers"><a href="#line143" name="line143">143</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line144">
<td class="line-numbers"><a href="#line144" name="line144">144</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (strpos($url, '/') === false) {</pre></td>
</tr>
<tr id="line145">
<td class="line-numbers"><a href="#line145" name="line145">145</a></td>
<td class="code"><pre class="prettyprint lang-php">            $server = $url;</pre></td>
</tr>
<tr id="line146">
<td class="line-numbers"><a href="#line146" name="line146">146</a></td>
<td class="code"><pre class="prettyprint lang-php">        } else {</pre></td>
</tr>
<tr id="line147">
<td class="line-numbers"><a href="#line147" name="line147">147</a></td>
<td class="code"><pre class="prettyprint lang-php">            $server = @parse_url($url, PHP_URL_HOST);</pre></td>
</tr>
<tr id="line148">
<td class="line-numbers"><a href="#line148" name="line148">148</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line149">
<td class="line-numbers"><a href="#line149" name="line149">149</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line150">
<td class="line-numbers"><a href="#line150" name="line150">150</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (!$server) {</pre></td>
</tr>
<tr id="line151">
<td class="line-numbers"><a href="#line151" name="line151">151</a></td>
<td class="code"><pre class="prettyprint lang-php">            return false;</pre></td>
</tr>
<tr id="line152">
<td class="line-numbers"><a href="#line152" name="line152">152</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line153">
<td class="line-numbers"><a href="#line153" name="line153">153</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line154">
<td class="line-numbers"><a href="#line154" name="line154">154</a></td>
<td class="code"><pre class="prettyprint lang-php">        return !!gethostbynamel($server);</pre></td>
</tr>
<tr id="line155">
<td class="line-numbers"><a href="#line155" name="line155">155</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line156">
<td class="line-numbers"><a href="#line156" name="line156">156</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line157">
<td class="line-numbers"><a href="#line157" name="line157">157</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function request_curl($url, $method='GET', $params=array())</pre></td>
</tr>
<tr id="line158">
<td class="line-numbers"><a href="#line158" name="line158">158</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line159">
<td class="line-numbers"><a href="#line159" name="line159">159</a></td>
<td class="code"><pre class="prettyprint lang-php">        $params = http_build_query($params, '', '&amp;');</pre></td>
</tr>
<tr id="line160">
<td class="line-numbers"><a href="#line160" name="line160">160</a></td>
<td class="code"><pre class="prettyprint lang-php">        $curl = curl_init($url . ($method == 'GET' &amp;&amp; $params ? '?' . $params : ''));</pre></td>
</tr>
<tr id="line161">
<td class="line-numbers"><a href="#line161" name="line161">161</a></td>
<td class="code"><pre class="prettyprint lang-php">        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);</pre></td>
</tr>
<tr id="line162">
<td class="line-numbers"><a href="#line162" name="line162">162</a></td>
<td class="code"><pre class="prettyprint lang-php">        curl_setopt($curl, CURLOPT_HEADER, false);</pre></td>
</tr>
<tr id="line163">
<td class="line-numbers"><a href="#line163" name="line163">163</a></td>
<td class="code"><pre class="prettyprint lang-php">        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);</pre></td>
</tr>
<tr id="line164">
<td class="line-numbers"><a href="#line164" name="line164">164</a></td>
<td class="code"><pre class="prettyprint lang-php">        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);</pre></td>
</tr>
<tr id="line165">
<td class="line-numbers"><a href="#line165" name="line165">165</a></td>
<td class="code"><pre class="prettyprint lang-php">        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/xrds+xml, */*'));</pre></td>
</tr>
<tr id="line166">
<td class="line-numbers"><a href="#line166" name="line166">166</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line167">
<td class="line-numbers"><a href="#line167" name="line167">167</a></td>
<td class="code"><pre class="prettyprint lang-php">        if($this-&gt;verify_peer !== null) {</pre></td>
</tr>
<tr id="line168">
<td class="line-numbers"><a href="#line168" name="line168">168</a></td>
<td class="code"><pre class="prettyprint lang-php">            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this-&gt;verify_peer);</pre></td>
</tr>
<tr id="line169">
<td class="line-numbers"><a href="#line169" name="line169">169</a></td>
<td class="code"><pre class="prettyprint lang-php">            if($this-&gt;capath) {</pre></td>
</tr>
<tr id="line170">
<td class="line-numbers"><a href="#line170" name="line170">170</a></td>
<td class="code"><pre class="prettyprint lang-php">                curl_setopt($curl, CURLOPT_CAPATH, $this-&gt;capath);</pre></td>
</tr>
<tr id="line171">
<td class="line-numbers"><a href="#line171" name="line171">171</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line172">
<td class="line-numbers"><a href="#line172" name="line172">172</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line173">
<td class="line-numbers"><a href="#line173" name="line173">173</a></td>
<td class="code"><pre class="prettyprint lang-php">            if($this-&gt;cainfo) {</pre></td>
</tr>
<tr id="line174">
<td class="line-numbers"><a href="#line174" name="line174">174</a></td>
<td class="code"><pre class="prettyprint lang-php">                curl_setopt($curl, CURLOPT_CAINFO, $this-&gt;cainfo);</pre></td>
</tr>
<tr id="line175">
<td class="line-numbers"><a href="#line175" name="line175">175</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line176">
<td class="line-numbers"><a href="#line176" name="line176">176</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line177">
<td class="line-numbers"><a href="#line177" name="line177">177</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line178">
<td class="line-numbers"><a href="#line178" name="line178">178</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ($method == 'POST') {</pre></td>
</tr>
<tr id="line179">
<td class="line-numbers"><a href="#line179" name="line179">179</a></td>
<td class="code"><pre class="prettyprint lang-php">            curl_setopt($curl, CURLOPT_POST, true);</pre></td>
</tr>
<tr id="line180">
<td class="line-numbers"><a href="#line180" name="line180">180</a></td>
<td class="code"><pre class="prettyprint lang-php">            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);</pre></td>
</tr>
<tr id="line181">
<td class="line-numbers"><a href="#line181" name="line181">181</a></td>
<td class="code"><pre class="prettyprint lang-php">        } elseif ($method == 'HEAD') {</pre></td>
</tr>
<tr id="line182">
<td class="line-numbers"><a href="#line182" name="line182">182</a></td>
<td class="code"><pre class="prettyprint lang-php">            curl_setopt($curl, CURLOPT_HEADER, true);</pre></td>
</tr>
<tr id="line183">
<td class="line-numbers"><a href="#line183" name="line183">183</a></td>
<td class="code"><pre class="prettyprint lang-php">            curl_setopt($curl, CURLOPT_NOBODY, true);</pre></td>
</tr>
<tr id="line184">
<td class="line-numbers"><a href="#line184" name="line184">184</a></td>
<td class="code"><pre class="prettyprint lang-php">        } else {</pre></td>
</tr>
<tr id="line185">
<td class="line-numbers"><a href="#line185" name="line185">185</a></td>
<td class="code"><pre class="prettyprint lang-php">            curl_setopt($curl, CURLOPT_HTTPGET, true);</pre></td>
</tr>
<tr id="line186">
<td class="line-numbers"><a href="#line186" name="line186">186</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line187">
<td class="line-numbers"><a href="#line187" name="line187">187</a></td>
<td class="code"><pre class="prettyprint lang-php">        $response = curl_exec($curl);</pre></td>
</tr>
<tr id="line188">
<td class="line-numbers"><a href="#line188" name="line188">188</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line189">
<td class="line-numbers"><a href="#line189" name="line189">189</a></td>
<td class="code"><pre class="prettyprint lang-php">        if($method == 'HEAD') {</pre></td>
</tr>
<tr id="line190">
<td class="line-numbers"><a href="#line190" name="line190">190</a></td>
<td class="code"><pre class="prettyprint lang-php">            $headers = array();</pre></td>
</tr>
<tr id="line191">
<td class="line-numbers"><a href="#line191" name="line191">191</a></td>
<td class="code"><pre class="prettyprint lang-php">            foreach(explode(&quot;\n&quot;, $response) as $header) {</pre></td>
</tr>
<tr id="line192">
<td class="line-numbers"><a href="#line192" name="line192">192</a></td>
<td class="code"><pre class="prettyprint lang-php">                $pos = strpos($header,':');</pre></td>
</tr>
<tr id="line193">
<td class="line-numbers"><a href="#line193" name="line193">193</a></td>
<td class="code"><pre class="prettyprint lang-php">                $name = strtolower(trim(substr($header, 0, $pos)));</pre></td>
</tr>
<tr id="line194">
<td class="line-numbers"><a href="#line194" name="line194">194</a></td>
<td class="code"><pre class="prettyprint lang-php">                $headers[$name] = trim(substr($header, $pos+1));</pre></td>
</tr>
<tr id="line195">
<td class="line-numbers"><a href="#line195" name="line195">195</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line196">
<td class="line-numbers"><a href="#line196" name="line196">196</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line197">
<td class="line-numbers"><a href="#line197" name="line197">197</a></td>
<td class="code"><pre class="prettyprint lang-php">            # Updating claimed_id in case of redirections.</pre></td>
</tr>
<tr id="line198">
<td class="line-numbers"><a href="#line198" name="line198">198</a></td>
<td class="code"><pre class="prettyprint lang-php">            $effective_url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);</pre></td>
</tr>
<tr id="line199">
<td class="line-numbers"><a href="#line199" name="line199">199</a></td>
<td class="code"><pre class="prettyprint lang-php">            if($effective_url != $url) {</pre></td>
</tr>
<tr id="line200">
<td class="line-numbers"><a href="#line200" name="line200">200</a></td>
<td class="code"><pre class="prettyprint lang-php">                $this-&gt;identity = $this-&gt;claimed_id = $effective_url;</pre></td>
</tr>
<tr id="line201">
<td class="line-numbers"><a href="#line201" name="line201">201</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line202">
<td class="line-numbers"><a href="#line202" name="line202">202</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line203">
<td class="line-numbers"><a href="#line203" name="line203">203</a></td>
<td class="code"><pre class="prettyprint lang-php">            return $headers;</pre></td>
</tr>
<tr id="line204">
<td class="line-numbers"><a href="#line204" name="line204">204</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line205">
<td class="line-numbers"><a href="#line205" name="line205">205</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line206">
<td class="line-numbers"><a href="#line206" name="line206">206</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (curl_errno($curl)) {</pre></td>
</tr>
<tr id="line207">
<td class="line-numbers"><a href="#line207" name="line207">207</a></td>
<td class="code"><pre class="prettyprint lang-php">            throw new ErrorException(curl_error($curl), curl_errno($curl));</pre></td>
</tr>
<tr id="line208">
<td class="line-numbers"><a href="#line208" name="line208">208</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line209">
<td class="line-numbers"><a href="#line209" name="line209">209</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line210">
<td class="line-numbers"><a href="#line210" name="line210">210</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $response;</pre></td>
</tr>
<tr id="line211">
<td class="line-numbers"><a href="#line211" name="line211">211</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line212">
<td class="line-numbers"><a href="#line212" name="line212">212</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line213">
<td class="line-numbers"><a href="#line213" name="line213">213</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function request_streams($url, $method='GET', $params=array())</pre></td>
</tr>
<tr id="line214">
<td class="line-numbers"><a href="#line214" name="line214">214</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line215">
<td class="line-numbers"><a href="#line215" name="line215">215</a></td>
<td class="code"><pre class="prettyprint lang-php">        if(!$this-&gt;hostExists($url)) {</pre></td>
</tr>
<tr id="line216">
<td class="line-numbers"><a href="#line216" name="line216">216</a></td>
<td class="code"><pre class="prettyprint lang-php">            throw new ErrorException(&quot;Could not connect to $url.&quot;, 404);</pre></td>
</tr>
<tr id="line217">
<td class="line-numbers"><a href="#line217" name="line217">217</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line218">
<td class="line-numbers"><a href="#line218" name="line218">218</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line219">
<td class="line-numbers"><a href="#line219" name="line219">219</a></td>
<td class="code"><pre class="prettyprint lang-php">        $params = http_build_query($params, '', '&amp;');</pre></td>
</tr>
<tr id="line220">
<td class="line-numbers"><a href="#line220" name="line220">220</a></td>
<td class="code"><pre class="prettyprint lang-php">        switch($method) {</pre></td>
</tr>
<tr id="line221">
<td class="line-numbers"><a href="#line221" name="line221">221</a></td>
<td class="code"><pre class="prettyprint lang-php">        case 'GET':</pre></td>
</tr>
<tr id="line222">
<td class="line-numbers"><a href="#line222" name="line222">222</a></td>
<td class="code"><pre class="prettyprint lang-php">            $opts = array(</pre></td>
</tr>
<tr id="line223">
<td class="line-numbers"><a href="#line223" name="line223">223</a></td>
<td class="code"><pre class="prettyprint lang-php">                'http' =&gt; array(</pre></td>
</tr>
<tr id="line224">
<td class="line-numbers"><a href="#line224" name="line224">224</a></td>
<td class="code"><pre class="prettyprint lang-php">                    'method' =&gt; 'GET',</pre></td>
</tr>
<tr id="line225">
<td class="line-numbers"><a href="#line225" name="line225">225</a></td>
<td class="code"><pre class="prettyprint lang-php">                    'header' =&gt; 'Accept: application/xrds+xml, */*',</pre></td>
</tr>
<tr id="line226">
<td class="line-numbers"><a href="#line226" name="line226">226</a></td>
<td class="code"><pre class="prettyprint lang-php">                    'ignore_errors' =&gt; true,</pre></td>
</tr>
<tr id="line227">
<td class="line-numbers"><a href="#line227" name="line227">227</a></td>
<td class="code"><pre class="prettyprint lang-php">                ), 'ssl' =&gt; array(</pre></td>
</tr>
<tr id="line228">
<td class="line-numbers"><a href="#line228" name="line228">228</a></td>
<td class="code"><pre class="prettyprint lang-php">                    'CN_match' =&gt; parse_url($url, PHP_URL_HOST),</pre></td>
</tr>
<tr id="line229">
<td class="line-numbers"><a href="#line229" name="line229">229</a></td>
<td class="code"><pre class="prettyprint lang-php">                ),</pre></td>
</tr>
<tr id="line230">
<td class="line-numbers"><a href="#line230" name="line230">230</a></td>
<td class="code"><pre class="prettyprint lang-php">            );</pre></td>
</tr>
<tr id="line231">
<td class="line-numbers"><a href="#line231" name="line231">231</a></td>
<td class="code"><pre class="prettyprint lang-php">            $url = $url . ($params ? '?' . $params : '');</pre></td>
</tr>
<tr id="line232">
<td class="line-numbers"><a href="#line232" name="line232">232</a></td>
<td class="code"><pre class="prettyprint lang-php">            break;</pre></td>
</tr>
<tr id="line233">
<td class="line-numbers"><a href="#line233" name="line233">233</a></td>
<td class="code"><pre class="prettyprint lang-php">        case 'POST':</pre></td>
</tr>
<tr id="line234">
<td class="line-numbers"><a href="#line234" name="line234">234</a></td>
<td class="code"><pre class="prettyprint lang-php">            $opts = array(</pre></td>
</tr>
<tr id="line235">
<td class="line-numbers"><a href="#line235" name="line235">235</a></td>
<td class="code"><pre class="prettyprint lang-php">                'http' =&gt; array(</pre></td>
</tr>
<tr id="line236">
<td class="line-numbers"><a href="#line236" name="line236">236</a></td>
<td class="code"><pre class="prettyprint lang-php">                    'method' =&gt; 'POST',</pre></td>
</tr>
<tr id="line237">
<td class="line-numbers"><a href="#line237" name="line237">237</a></td>
<td class="code"><pre class="prettyprint lang-php">                    'header'  =&gt; 'Content-type: application/x-www-form-urlencoded',</pre></td>
</tr>
<tr id="line238">
<td class="line-numbers"><a href="#line238" name="line238">238</a></td>
<td class="code"><pre class="prettyprint lang-php">                    'content' =&gt; $params,</pre></td>
</tr>
<tr id="line239">
<td class="line-numbers"><a href="#line239" name="line239">239</a></td>
<td class="code"><pre class="prettyprint lang-php">                    'ignore_errors' =&gt; true,</pre></td>
</tr>
<tr id="line240">
<td class="line-numbers"><a href="#line240" name="line240">240</a></td>
<td class="code"><pre class="prettyprint lang-php">                ), 'ssl' =&gt; array(</pre></td>
</tr>
<tr id="line241">
<td class="line-numbers"><a href="#line241" name="line241">241</a></td>
<td class="code"><pre class="prettyprint lang-php">                    'CN_match' =&gt; parse_url($url, PHP_URL_HOST),</pre></td>
</tr>
<tr id="line242">
<td class="line-numbers"><a href="#line242" name="line242">242</a></td>
<td class="code"><pre class="prettyprint lang-php">                ),</pre></td>
</tr>
<tr id="line243">
<td class="line-numbers"><a href="#line243" name="line243">243</a></td>
<td class="code"><pre class="prettyprint lang-php">            );</pre></td>
</tr>
<tr id="line244">
<td class="line-numbers"><a href="#line244" name="line244">244</a></td>
<td class="code"><pre class="prettyprint lang-php">            break;</pre></td>
</tr>
<tr id="line245">
<td class="line-numbers"><a href="#line245" name="line245">245</a></td>
<td class="code"><pre class="prettyprint lang-php">        case 'HEAD':</pre></td>
</tr>
<tr id="line246">
<td class="line-numbers"><a href="#line246" name="line246">246</a></td>
<td class="code"><pre class="prettyprint lang-php">            # We want to send a HEAD request,</pre></td>
</tr>
<tr id="line247">
<td class="line-numbers"><a href="#line247" name="line247">247</a></td>
<td class="code"><pre class="prettyprint lang-php">            # but since get_headers doesn't accept $context parameter,</pre></td>
</tr>
<tr id="line248">
<td class="line-numbers"><a href="#line248" name="line248">248</a></td>
<td class="code"><pre class="prettyprint lang-php">            # we have to change the defaults.</pre></td>
</tr>
<tr id="line249">
<td class="line-numbers"><a href="#line249" name="line249">249</a></td>
<td class="code"><pre class="prettyprint lang-php">            $default = stream_context_get_options(stream_context_get_default());</pre></td>
</tr>
<tr id="line250">
<td class="line-numbers"><a href="#line250" name="line250">250</a></td>
<td class="code"><pre class="prettyprint lang-php">            stream_context_get_default(</pre></td>
</tr>
<tr id="line251">
<td class="line-numbers"><a href="#line251" name="line251">251</a></td>
<td class="code"><pre class="prettyprint lang-php">                array(</pre></td>
</tr>
<tr id="line252">
<td class="line-numbers"><a href="#line252" name="line252">252</a></td>
<td class="code"><pre class="prettyprint lang-php">                    'http' =&gt; array(</pre></td>
</tr>
<tr id="line253">
<td class="line-numbers"><a href="#line253" name="line253">253</a></td>
<td class="code"><pre class="prettyprint lang-php">                        'method' =&gt; 'HEAD',</pre></td>
</tr>
<tr id="line254">
<td class="line-numbers"><a href="#line254" name="line254">254</a></td>
<td class="code"><pre class="prettyprint lang-php">                        'header' =&gt; 'Accept: application/xrds+xml, */*',</pre></td>
</tr>
<tr id="line255">
<td class="line-numbers"><a href="#line255" name="line255">255</a></td>
<td class="code"><pre class="prettyprint lang-php">                        'ignore_errors' =&gt; true,</pre></td>
</tr>
<tr id="line256">
<td class="line-numbers"><a href="#line256" name="line256">256</a></td>
<td class="code"><pre class="prettyprint lang-php">                    ), 'ssl' =&gt; array(</pre></td>
</tr>
<tr id="line257">
<td class="line-numbers"><a href="#line257" name="line257">257</a></td>
<td class="code"><pre class="prettyprint lang-php">                        'CN_match' =&gt; parse_url($url, PHP_URL_HOST),</pre></td>
</tr>
<tr id="line258">
<td class="line-numbers"><a href="#line258" name="line258">258</a></td>
<td class="code"><pre class="prettyprint lang-php">                    ),</pre></td>
</tr>
<tr id="line259">
<td class="line-numbers"><a href="#line259" name="line259">259</a></td>
<td class="code"><pre class="prettyprint lang-php">                )</pre></td>
</tr>
<tr id="line260">
<td class="line-numbers"><a href="#line260" name="line260">260</a></td>
<td class="code"><pre class="prettyprint lang-php">            );</pre></td>
</tr>
<tr id="line261">
<td class="line-numbers"><a href="#line261" name="line261">261</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line262">
<td class="line-numbers"><a href="#line262" name="line262">262</a></td>
<td class="code"><pre class="prettyprint lang-php">            $url = $url . ($params ? '?' . $params : '');</pre></td>
</tr>
<tr id="line263">
<td class="line-numbers"><a href="#line263" name="line263">263</a></td>
<td class="code"><pre class="prettyprint lang-php">            $headers_tmp = get_headers ($url);</pre></td>
</tr>
<tr id="line264">
<td class="line-numbers"><a href="#line264" name="line264">264</a></td>
<td class="code"><pre class="prettyprint lang-php">            if(!$headers_tmp) {</pre></td>
</tr>
<tr id="line265">
<td class="line-numbers"><a href="#line265" name="line265">265</a></td>
<td class="code"><pre class="prettyprint lang-php">                return array();</pre></td>
</tr>
<tr id="line266">
<td class="line-numbers"><a href="#line266" name="line266">266</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line267">
<td class="line-numbers"><a href="#line267" name="line267">267</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line268">
<td class="line-numbers"><a href="#line268" name="line268">268</a></td>
<td class="code"><pre class="prettyprint lang-php">            # Parsing headers.</pre></td>
</tr>
<tr id="line269">
<td class="line-numbers"><a href="#line269" name="line269">269</a></td>
<td class="code"><pre class="prettyprint lang-php">            $headers = array();</pre></td>
</tr>
<tr id="line270">
<td class="line-numbers"><a href="#line270" name="line270">270</a></td>
<td class="code"><pre class="prettyprint lang-php">            foreach($headers_tmp as $header) {</pre></td>
</tr>
<tr id="line271">
<td class="line-numbers"><a href="#line271" name="line271">271</a></td>
<td class="code"><pre class="prettyprint lang-php">                $pos = strpos($header,':');</pre></td>
</tr>
<tr id="line272">
<td class="line-numbers"><a href="#line272" name="line272">272</a></td>
<td class="code"><pre class="prettyprint lang-php">                $name = strtolower(trim(substr($header, 0, $pos)));</pre></td>
</tr>
<tr id="line273">
<td class="line-numbers"><a href="#line273" name="line273">273</a></td>
<td class="code"><pre class="prettyprint lang-php">                $headers[$name] = trim(substr($header, $pos+1));</pre></td>
</tr>
<tr id="line274">
<td class="line-numbers"><a href="#line274" name="line274">274</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line275">
<td class="line-numbers"><a href="#line275" name="line275">275</a></td>
<td class="code"><pre class="prettyprint lang-php">                # Following possible redirections. The point is just to have</pre></td>
</tr>
<tr id="line276">
<td class="line-numbers"><a href="#line276" name="line276">276</a></td>
<td class="code"><pre class="prettyprint lang-php">                # claimed_id change with them, because get_headers() will</pre></td>
</tr>
<tr id="line277">
<td class="line-numbers"><a href="#line277" name="line277">277</a></td>
<td class="code"><pre class="prettyprint lang-php">                # follow redirections automatically.</pre></td>
</tr>
<tr id="line278">
<td class="line-numbers"><a href="#line278" name="line278">278</a></td>
<td class="code"><pre class="prettyprint lang-php">                # We ignore redirections with relative paths.</pre></td>
</tr>
<tr id="line279">
<td class="line-numbers"><a href="#line279" name="line279">279</a></td>
<td class="code"><pre class="prettyprint lang-php">                # If any known provider uses them, file a bug report.</pre></td>
</tr>
<tr id="line280">
<td class="line-numbers"><a href="#line280" name="line280">280</a></td>
<td class="code"><pre class="prettyprint lang-php">                if($name == 'location') {</pre></td>
</tr>
<tr id="line281">
<td class="line-numbers"><a href="#line281" name="line281">281</a></td>
<td class="code"><pre class="prettyprint lang-php">                    if(strpos($headers[$name], 'http') === 0) {</pre></td>
</tr>
<tr id="line282">
<td class="line-numbers"><a href="#line282" name="line282">282</a></td>
<td class="code"><pre class="prettyprint lang-php">                        $this-&gt;identity = $this-&gt;claimed_id = $headers[$name];</pre></td>
</tr>
<tr id="line283">
<td class="line-numbers"><a href="#line283" name="line283">283</a></td>
<td class="code"><pre class="prettyprint lang-php">                    } elseif($headers[$name][0] == '/') {</pre></td>
</tr>
<tr id="line284">
<td class="line-numbers"><a href="#line284" name="line284">284</a></td>
<td class="code"><pre class="prettyprint lang-php">                        $parsed_url = parse_url($this-&gt;claimed_id);</pre></td>
</tr>
<tr id="line285">
<td class="line-numbers"><a href="#line285" name="line285">285</a></td>
<td class="code"><pre class="prettyprint lang-php">                        $this-&gt;identity =</pre></td>
</tr>
<tr id="line286">
<td class="line-numbers"><a href="#line286" name="line286">286</a></td>
<td class="code"><pre class="prettyprint lang-php">                        $this-&gt;claimed_id = $parsed_url['scheme'] . '://'</pre></td>
</tr>
<tr id="line287">
<td class="line-numbers"><a href="#line287" name="line287">287</a></td>
<td class="code"><pre class="prettyprint lang-php">                                          . $parsed_url['host']</pre></td>
</tr>
<tr id="line288">
<td class="line-numbers"><a href="#line288" name="line288">288</a></td>
<td class="code"><pre class="prettyprint lang-php">                                          . $headers[$name];</pre></td>
</tr>
<tr id="line289">
<td class="line-numbers"><a href="#line289" name="line289">289</a></td>
<td class="code"><pre class="prettyprint lang-php">                    }</pre></td>
</tr>
<tr id="line290">
<td class="line-numbers"><a href="#line290" name="line290">290</a></td>
<td class="code"><pre class="prettyprint lang-php">                }</pre></td>
</tr>
<tr id="line291">
<td class="line-numbers"><a href="#line291" name="line291">291</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line292">
<td class="line-numbers"><a href="#line292" name="line292">292</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line293">
<td class="line-numbers"><a href="#line293" name="line293">293</a></td>
<td class="code"><pre class="prettyprint lang-php">            # And restore them.</pre></td>
</tr>
<tr id="line294">
<td class="line-numbers"><a href="#line294" name="line294">294</a></td>
<td class="code"><pre class="prettyprint lang-php">            stream_context_get_default($default);</pre></td>
</tr>
<tr id="line295">
<td class="line-numbers"><a href="#line295" name="line295">295</a></td>
<td class="code"><pre class="prettyprint lang-php">            return $headers;</pre></td>
</tr>
<tr id="line296">
<td class="line-numbers"><a href="#line296" name="line296">296</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line297">
<td class="line-numbers"><a href="#line297" name="line297">297</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line298">
<td class="line-numbers"><a href="#line298" name="line298">298</a></td>
<td class="code"><pre class="prettyprint lang-php">        if($this-&gt;verify_peer) {</pre></td>
</tr>
<tr id="line299">
<td class="line-numbers"><a href="#line299" name="line299">299</a></td>
<td class="code"><pre class="prettyprint lang-php">            $opts['ssl'] += array(</pre></td>
</tr>
<tr id="line300">
<td class="line-numbers"><a href="#line300" name="line300">300</a></td>
<td class="code"><pre class="prettyprint lang-php">                'verify_peer' =&gt; true,</pre></td>
</tr>
<tr id="line301">
<td class="line-numbers"><a href="#line301" name="line301">301</a></td>
<td class="code"><pre class="prettyprint lang-php">                'capath'      =&gt; $this-&gt;capath,</pre></td>
</tr>
<tr id="line302">
<td class="line-numbers"><a href="#line302" name="line302">302</a></td>
<td class="code"><pre class="prettyprint lang-php">                'cafile'      =&gt; $this-&gt;cainfo,</pre></td>
</tr>
<tr id="line303">
<td class="line-numbers"><a href="#line303" name="line303">303</a></td>
<td class="code"><pre class="prettyprint lang-php">            );</pre></td>
</tr>
<tr id="line304">
<td class="line-numbers"><a href="#line304" name="line304">304</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line305">
<td class="line-numbers"><a href="#line305" name="line305">305</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line306">
<td class="line-numbers"><a href="#line306" name="line306">306</a></td>
<td class="code"><pre class="prettyprint lang-php">        $context = stream_context_create ($opts);</pre></td>
</tr>
<tr id="line307">
<td class="line-numbers"><a href="#line307" name="line307">307</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line308">
<td class="line-numbers"><a href="#line308" name="line308">308</a></td>
<td class="code"><pre class="prettyprint lang-php">        return file_get_contents($url, false, $context);</pre></td>
</tr>
<tr id="line309">
<td class="line-numbers"><a href="#line309" name="line309">309</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line310">
<td class="line-numbers"><a href="#line310" name="line310">310</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line311">
<td class="line-numbers"><a href="#line311" name="line311">311</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function request($url, $method='GET', $params=array())</pre></td>
</tr>
<tr id="line312">
<td class="line-numbers"><a href="#line312" name="line312">312</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line313">
<td class="line-numbers"><a href="#line313" name="line313">313</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (function_exists('curl_init')</pre></td>
</tr>
<tr id="line314">
<td class="line-numbers"><a href="#line314" name="line314">314</a></td>
<td class="code"><pre class="prettyprint lang-php">            &amp;&amp; (!in_array('https', stream_get_wrappers()) || !ini_get('safe_mode') &amp;&amp; !ini_get('open_basedir'))</pre></td>
</tr>
<tr id="line315">
<td class="line-numbers"><a href="#line315" name="line315">315</a></td>
<td class="code"><pre class="prettyprint lang-php">        ) {</pre></td>
</tr>
<tr id="line316">
<td class="line-numbers"><a href="#line316" name="line316">316</a></td>
<td class="code"><pre class="prettyprint lang-php">            return $this-&gt;request_curl($url, $method, $params);</pre></td>
</tr>
<tr id="line317">
<td class="line-numbers"><a href="#line317" name="line317">317</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line318">
<td class="line-numbers"><a href="#line318" name="line318">318</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $this-&gt;request_streams($url, $method, $params);</pre></td>
</tr>
<tr id="line319">
<td class="line-numbers"><a href="#line319" name="line319">319</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line320">
<td class="line-numbers"><a href="#line320" name="line320">320</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line321">
<td class="line-numbers"><a href="#line321" name="line321">321</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function build_url($url, $parts)</pre></td>
</tr>
<tr id="line322">
<td class="line-numbers"><a href="#line322" name="line322">322</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line323">
<td class="line-numbers"><a href="#line323" name="line323">323</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (isset($url['query'], $parts['query'])) {</pre></td>
</tr>
<tr id="line324">
<td class="line-numbers"><a href="#line324" name="line324">324</a></td>
<td class="code"><pre class="prettyprint lang-php">            $parts['query'] = $url['query'] . '&amp;' . $parts['query'];</pre></td>
</tr>
<tr id="line325">
<td class="line-numbers"><a href="#line325" name="line325">325</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line326">
<td class="line-numbers"><a href="#line326" name="line326">326</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line327">
<td class="line-numbers"><a href="#line327" name="line327">327</a></td>
<td class="code"><pre class="prettyprint lang-php">        $url = $parts + $url;</pre></td>
</tr>
<tr id="line328">
<td class="line-numbers"><a href="#line328" name="line328">328</a></td>
<td class="code"><pre class="prettyprint lang-php">        $url = $url['scheme'] . '://'</pre></td>
</tr>
<tr id="line329">
<td class="line-numbers"><a href="#line329" name="line329">329</a></td>
<td class="code"><pre class="prettyprint lang-php">             . (empty($url['username'])?''</pre></td>
</tr>
<tr id="line330">
<td class="line-numbers"><a href="#line330" name="line330">330</a></td>
<td class="code"><pre class="prettyprint lang-php">                 :(empty($url['password'])? &quot;{$url['username']}@&quot;</pre></td>
</tr>
<tr id="line331">
<td class="line-numbers"><a href="#line331" name="line331">331</a></td>
<td class="code"><pre class="prettyprint lang-php">                 :&quot;{$url['username']}:{$url['password']}@&quot;))</pre></td>
</tr>
<tr id="line332">
<td class="line-numbers"><a href="#line332" name="line332">332</a></td>
<td class="code"><pre class="prettyprint lang-php">             . $url['host']</pre></td>
</tr>
<tr id="line333">
<td class="line-numbers"><a href="#line333" name="line333">333</a></td>
<td class="code"><pre class="prettyprint lang-php">             . (empty($url['port'])?'':&quot;:{$url['port']}&quot;)</pre></td>
</tr>
<tr id="line334">
<td class="line-numbers"><a href="#line334" name="line334">334</a></td>
<td class="code"><pre class="prettyprint lang-php">             . (empty($url['path'])?'':$url['path'])</pre></td>
</tr>
<tr id="line335">
<td class="line-numbers"><a href="#line335" name="line335">335</a></td>
<td class="code"><pre class="prettyprint lang-php">             . (empty($url['query'])?'':&quot;?{$url['query']}&quot;)</pre></td>
</tr>
<tr id="line336">
<td class="line-numbers"><a href="#line336" name="line336">336</a></td>
<td class="code"><pre class="prettyprint lang-php">             . (empty($url['fragment'])?'':&quot;#{$url['fragment']}&quot;);</pre></td>
</tr>
<tr id="line337">
<td class="line-numbers"><a href="#line337" name="line337">337</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $url;</pre></td>
</tr>
<tr id="line338">
<td class="line-numbers"><a href="#line338" name="line338">338</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line339">
<td class="line-numbers"><a href="#line339" name="line339">339</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line340">
<td class="line-numbers"><a href="#line340" name="line340">340</a></td>
<td class="code"><pre class="prettyprint lang-php">    /**</pre></td>
</tr>
<tr id="line341">
<td class="line-numbers"><a href="#line341" name="line341">341</a></td>
<td class="code"><pre class="prettyprint lang-php">     * Helper function used to scan for &lt;meta&gt;/&lt;link&gt; tags and extract information</pre></td>
</tr>
<tr id="line342">
<td class="line-numbers"><a href="#line342" name="line342">342</a></td>
<td class="code"><pre class="prettyprint lang-php">     * from them</pre></td>
</tr>
<tr id="line343">
<td class="line-numbers"><a href="#line343" name="line343">343</a></td>
<td class="code"><pre class="prettyprint lang-php">     */</pre></td>
</tr>
<tr id="line344">
<td class="line-numbers"><a href="#line344" name="line344">344</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function htmlTag($content, $tag, $attrName, $attrValue, $valueName)</pre></td>
</tr>
<tr id="line345">
<td class="line-numbers"><a href="#line345" name="line345">345</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line346">
<td class="line-numbers"><a href="#line346" name="line346">346</a></td>
<td class="code"><pre class="prettyprint lang-php">        preg_match_all(&quot;#&lt;{$tag}[^&gt;]*$attrName=['\&quot;].*?$attrValue.*?['\&quot;][^&gt;]*$valueName=['\&quot;](.+?)['\&quot;][^&gt;]*/?&gt;#i&quot;, $content, $matches1);</pre></td>
</tr>
<tr id="line347">
<td class="line-numbers"><a href="#line347" name="line347">347</a></td>
<td class="code"><pre class="prettyprint lang-php">        preg_match_all(&quot;#&lt;{$tag}[^&gt;]*$valueName=['\&quot;](.+?)['\&quot;][^&gt;]*$attrName=['\&quot;].*?$attrValue.*?['\&quot;][^&gt;]*/?&gt;#i&quot;, $content, $matches2);</pre></td>
</tr>
<tr id="line348">
<td class="line-numbers"><a href="#line348" name="line348">348</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line349">
<td class="line-numbers"><a href="#line349" name="line349">349</a></td>
<td class="code"><pre class="prettyprint lang-php">        $result = array_merge($matches1[1], $matches2[1]);</pre></td>
</tr>
<tr id="line350">
<td class="line-numbers"><a href="#line350" name="line350">350</a></td>
<td class="code"><pre class="prettyprint lang-php">        return empty($result)?false:$result[0];</pre></td>
</tr>
<tr id="line351">
<td class="line-numbers"><a href="#line351" name="line351">351</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line352">
<td class="line-numbers"><a href="#line352" name="line352">352</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line353">
<td class="line-numbers"><a href="#line353" name="line353">353</a></td>
<td class="code"><pre class="prettyprint lang-php">    /**</pre></td>
</tr>
<tr id="line354">
<td class="line-numbers"><a href="#line354" name="line354">354</a></td>
<td class="code"><pre class="prettyprint lang-php">     * Performs Yadis and HTML discovery. Normally not used.</pre></td>
</tr>
<tr id="line355">
<td class="line-numbers"><a href="#line355" name="line355">355</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @param $url Identity URL.</pre></td>
</tr>
<tr id="line356">
<td class="line-numbers"><a href="#line356" name="line356">356</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @return String OP Endpoint (i.e. OpenID provider address).</pre></td>
</tr>
<tr id="line357">
<td class="line-numbers"><a href="#line357" name="line357">357</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @throws ErrorException</pre></td>
</tr>
<tr id="line358">
<td class="line-numbers"><a href="#line358" name="line358">358</a></td>
<td class="code"><pre class="prettyprint lang-php">     */</pre></td>
</tr>
<tr id="line359">
<td class="line-numbers"><a href="#line359" name="line359">359</a></td>
<td class="code"><pre class="prettyprint lang-php">    function discover($url)</pre></td>
</tr>
<tr id="line360">
<td class="line-numbers"><a href="#line360" name="line360">360</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line361">
<td class="line-numbers"><a href="#line361" name="line361">361</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (!$url) throw new ErrorException('No identity supplied.');</pre></td>
</tr>
<tr id="line362">
<td class="line-numbers"><a href="#line362" name="line362">362</a></td>
<td class="code"><pre class="prettyprint lang-php">        # Use xri.net proxy to resolve i-name identities</pre></td>
</tr>
<tr id="line363">
<td class="line-numbers"><a href="#line363" name="line363">363</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (!preg_match('#^https?:#', $url)) {</pre></td>
</tr>
<tr id="line364">
<td class="line-numbers"><a href="#line364" name="line364">364</a></td>
<td class="code"><pre class="prettyprint lang-php">            $url = &quot;https://xri.net/$url&quot;;</pre></td>
</tr>
<tr id="line365">
<td class="line-numbers"><a href="#line365" name="line365">365</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line366">
<td class="line-numbers"><a href="#line366" name="line366">366</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line367">
<td class="line-numbers"><a href="#line367" name="line367">367</a></td>
<td class="code"><pre class="prettyprint lang-php">        # We save the original url in case of Yadis discovery failure.</pre></td>
</tr>
<tr id="line368">
<td class="line-numbers"><a href="#line368" name="line368">368</a></td>
<td class="code"><pre class="prettyprint lang-php">        # It can happen when we'll be lead to an XRDS document</pre></td>
</tr>
<tr id="line369">
<td class="line-numbers"><a href="#line369" name="line369">369</a></td>
<td class="code"><pre class="prettyprint lang-php">        # which does not have any OpenID2 services.</pre></td>
</tr>
<tr id="line370">
<td class="line-numbers"><a href="#line370" name="line370">370</a></td>
<td class="code"><pre class="prettyprint lang-php">        $originalUrl = $url;</pre></td>
</tr>
<tr id="line371">
<td class="line-numbers"><a href="#line371" name="line371">371</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line372">
<td class="line-numbers"><a href="#line372" name="line372">372</a></td>
<td class="code"><pre class="prettyprint lang-php">        # A flag to disable yadis discovery in case of failure in headers.</pre></td>
</tr>
<tr id="line373">
<td class="line-numbers"><a href="#line373" name="line373">373</a></td>
<td class="code"><pre class="prettyprint lang-php">        $yadis = true;</pre></td>
</tr>
<tr id="line374">
<td class="line-numbers"><a href="#line374" name="line374">374</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line375">
<td class="line-numbers"><a href="#line375" name="line375">375</a></td>
<td class="code"><pre class="prettyprint lang-php">        # We'll jump a maximum of 5 times, to avoid endless redirections.</pre></td>
</tr>
<tr id="line376">
<td class="line-numbers"><a href="#line376" name="line376">376</a></td>
<td class="code"><pre class="prettyprint lang-php">        for ($i = 0; $i &lt; 5; $i ++) {</pre></td>
</tr>
<tr id="line377">
<td class="line-numbers"><a href="#line377" name="line377">377</a></td>
<td class="code"><pre class="prettyprint lang-php">            if ($yadis) {</pre></td>
</tr>
<tr id="line378">
<td class="line-numbers"><a href="#line378" name="line378">378</a></td>
<td class="code"><pre class="prettyprint lang-php">                $headers = $this-&gt;request($url, 'HEAD');</pre></td>
</tr>
<tr id="line379">
<td class="line-numbers"><a href="#line379" name="line379">379</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line380">
<td class="line-numbers"><a href="#line380" name="line380">380</a></td>
<td class="code"><pre class="prettyprint lang-php">                $next = false;</pre></td>
</tr>
<tr id="line381">
<td class="line-numbers"><a href="#line381" name="line381">381</a></td>
<td class="code"><pre class="prettyprint lang-php">                if (isset($headers['x-xrds-location'])) {</pre></td>
</tr>
<tr id="line382">
<td class="line-numbers"><a href="#line382" name="line382">382</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $url = $this-&gt;build_url(parse_url($url), parse_url(trim($headers['x-xrds-location'])));</pre></td>
</tr>
<tr id="line383">
<td class="line-numbers"><a href="#line383" name="line383">383</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $next = true;</pre></td>
</tr>
<tr id="line384">
<td class="line-numbers"><a href="#line384" name="line384">384</a></td>
<td class="code"><pre class="prettyprint lang-php">                }</pre></td>
</tr>
<tr id="line385">
<td class="line-numbers"><a href="#line385" name="line385">385</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line386">
<td class="line-numbers"><a href="#line386" name="line386">386</a></td>
<td class="code"><pre class="prettyprint lang-php">                if (isset($headers['content-type'])</pre></td>
</tr>
<tr id="line387">
<td class="line-numbers"><a href="#line387" name="line387">387</a></td>
<td class="code"><pre class="prettyprint lang-php">                    &amp;&amp; (strpos($headers['content-type'], 'application/xrds+xml') !== false</pre></td>
</tr>
<tr id="line388">
<td class="line-numbers"><a href="#line388" name="line388">388</a></td>
<td class="code"><pre class="prettyprint lang-php">                        || strpos($headers['content-type'], 'text/xml') !== false)</pre></td>
</tr>
<tr id="line389">
<td class="line-numbers"><a href="#line389" name="line389">389</a></td>
<td class="code"><pre class="prettyprint lang-php">                ) {</pre></td>
</tr>
<tr id="line390">
<td class="line-numbers"><a href="#line390" name="line390">390</a></td>
<td class="code"><pre class="prettyprint lang-php">                    # Apparently, some providers return XRDS documents as text/html.</pre></td>
</tr>
<tr id="line391">
<td class="line-numbers"><a href="#line391" name="line391">391</a></td>
<td class="code"><pre class="prettyprint lang-php">                    # While it is against the spec, allowing this here shouldn't break</pre></td>
</tr>
<tr id="line392">
<td class="line-numbers"><a href="#line392" name="line392">392</a></td>
<td class="code"><pre class="prettyprint lang-php">                    # compatibility with anything.</pre></td>
</tr>
<tr id="line393">
<td class="line-numbers"><a href="#line393" name="line393">393</a></td>
<td class="code"><pre class="prettyprint lang-php">                    # ---</pre></td>
</tr>
<tr id="line394">
<td class="line-numbers"><a href="#line394" name="line394">394</a></td>
<td class="code"><pre class="prettyprint lang-php">                    # Found an XRDS document, now let's find the server, and optionally delegate.</pre></td>
</tr>
<tr id="line395">
<td class="line-numbers"><a href="#line395" name="line395">395</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $content = $this-&gt;request($url, 'GET');</pre></td>
</tr>
<tr id="line396">
<td class="line-numbers"><a href="#line396" name="line396">396</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line397">
<td class="line-numbers"><a href="#line397" name="line397">397</a></td>
<td class="code"><pre class="prettyprint lang-php">                    preg_match_all('#&lt;Service.*?&gt;(.*?)&lt;/Service&gt;#s', $content, $m);</pre></td>
</tr>
<tr id="line398">
<td class="line-numbers"><a href="#line398" name="line398">398</a></td>
<td class="code"><pre class="prettyprint lang-php">                    foreach($m[1] as $content) {</pre></td>
</tr>
<tr id="line399">
<td class="line-numbers"><a href="#line399" name="line399">399</a></td>
<td class="code"><pre class="prettyprint lang-php">                        $content = ' ' . $content; # The space is added, so that strpos doesn't return 0.</pre></td>
</tr>
<tr id="line400">
<td class="line-numbers"><a href="#line400" name="line400">400</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line401">
<td class="line-numbers"><a href="#line401" name="line401">401</a></td>
<td class="code"><pre class="prettyprint lang-php">                        # OpenID 2</pre></td>
</tr>
<tr id="line402">
<td class="line-numbers"><a href="#line402" name="line402">402</a></td>
<td class="code"><pre class="prettyprint lang-php">                        $ns = preg_quote('http://specs.openid.net/auth/2.0/');</pre></td>
</tr>
<tr id="line403">
<td class="line-numbers"><a href="#line403" name="line403">403</a></td>
<td class="code"><pre class="prettyprint lang-php">                        if(preg_match('#&lt;Type&gt;\s*'.$ns.'(server|signon)\s*&lt;/Type&gt;#s', $content, $type)) {</pre></td>
</tr>
<tr id="line404">
<td class="line-numbers"><a href="#line404" name="line404">404</a></td>
<td class="code"><pre class="prettyprint lang-php">                            if ($type[1] == 'server') $this-&gt;identifier_select = true;</pre></td>
</tr>
<tr id="line405">
<td class="line-numbers"><a href="#line405" name="line405">405</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line406">
<td class="line-numbers"><a href="#line406" name="line406">406</a></td>
<td class="code"><pre class="prettyprint lang-php">                            preg_match('#&lt;URI.*?&gt;(.*)&lt;/URI&gt;#', $content, $server);</pre></td>
</tr>
<tr id="line407">
<td class="line-numbers"><a href="#line407" name="line407">407</a></td>
<td class="code"><pre class="prettyprint lang-php">                            preg_match('#&lt;(Local|Canonical)ID&gt;(.*)&lt;/\1ID&gt;#', $content, $delegate);</pre></td>
</tr>
<tr id="line408">
<td class="line-numbers"><a href="#line408" name="line408">408</a></td>
<td class="code"><pre class="prettyprint lang-php">                            if (empty($server)) {</pre></td>
</tr>
<tr id="line409">
<td class="line-numbers"><a href="#line409" name="line409">409</a></td>
<td class="code"><pre class="prettyprint lang-php">                                return false;</pre></td>
</tr>
<tr id="line410">
<td class="line-numbers"><a href="#line410" name="line410">410</a></td>
<td class="code"><pre class="prettyprint lang-php">                            }</pre></td>
</tr>
<tr id="line411">
<td class="line-numbers"><a href="#line411" name="line411">411</a></td>
<td class="code"><pre class="prettyprint lang-php">                            # Does the server advertise support for either AX or SREG?</pre></td>
</tr>
<tr id="line412">
<td class="line-numbers"><a href="#line412" name="line412">412</a></td>
<td class="code"><pre class="prettyprint lang-php">                            $this-&gt;ax   = (bool) strpos($content, '&lt;Type&gt;http://openid.net/srv/ax/1.0&lt;/Type&gt;');</pre></td>
</tr>
<tr id="line413">
<td class="line-numbers"><a href="#line413" name="line413">413</a></td>
<td class="code"><pre class="prettyprint lang-php">                            $this-&gt;sreg = strpos($content, '&lt;Type&gt;http://openid.net/sreg/1.0&lt;/Type&gt;')</pre></td>
</tr>
<tr id="line414">
<td class="line-numbers"><a href="#line414" name="line414">414</a></td>
<td class="code"><pre class="prettyprint lang-php">                                       || strpos($content, '&lt;Type&gt;http://openid.net/extensions/sreg/1.1&lt;/Type&gt;');</pre></td>
</tr>
<tr id="line415">
<td class="line-numbers"><a href="#line415" name="line415">415</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line416">
<td class="line-numbers"><a href="#line416" name="line416">416</a></td>
<td class="code"><pre class="prettyprint lang-php">                            $server = $server[1];</pre></td>
</tr>
<tr id="line417">
<td class="line-numbers"><a href="#line417" name="line417">417</a></td>
<td class="code"><pre class="prettyprint lang-php">                            if (isset($delegate[2])) $this-&gt;identity = trim($delegate[2]);</pre></td>
</tr>
<tr id="line418">
<td class="line-numbers"><a href="#line418" name="line418">418</a></td>
<td class="code"><pre class="prettyprint lang-php">                            $this-&gt;version = 2;</pre></td>
</tr>
<tr id="line419">
<td class="line-numbers"><a href="#line419" name="line419">419</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line420">
<td class="line-numbers"><a href="#line420" name="line420">420</a></td>
<td class="code"><pre class="prettyprint lang-php">                            $this-&gt;server = $server;</pre></td>
</tr>
<tr id="line421">
<td class="line-numbers"><a href="#line421" name="line421">421</a></td>
<td class="code"><pre class="prettyprint lang-php">                            return $server;</pre></td>
</tr>
<tr id="line422">
<td class="line-numbers"><a href="#line422" name="line422">422</a></td>
<td class="code"><pre class="prettyprint lang-php">                        }</pre></td>
</tr>
<tr id="line423">
<td class="line-numbers"><a href="#line423" name="line423">423</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line424">
<td class="line-numbers"><a href="#line424" name="line424">424</a></td>
<td class="code"><pre class="prettyprint lang-php">                        # OpenID 1.1</pre></td>
</tr>
<tr id="line425">
<td class="line-numbers"><a href="#line425" name="line425">425</a></td>
<td class="code"><pre class="prettyprint lang-php">                        $ns = preg_quote('http://openid.net/signon/1.1');</pre></td>
</tr>
<tr id="line426">
<td class="line-numbers"><a href="#line426" name="line426">426</a></td>
<td class="code"><pre class="prettyprint lang-php">                        if (preg_match('#&lt;Type&gt;\s*'.$ns.'\s*&lt;/Type&gt;#s', $content)) {</pre></td>
</tr>
<tr id="line427">
<td class="line-numbers"><a href="#line427" name="line427">427</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line428">
<td class="line-numbers"><a href="#line428" name="line428">428</a></td>
<td class="code"><pre class="prettyprint lang-php">                            preg_match('#&lt;URI.*?&gt;(.*)&lt;/URI&gt;#', $content, $server);</pre></td>
</tr>
<tr id="line429">
<td class="line-numbers"><a href="#line429" name="line429">429</a></td>
<td class="code"><pre class="prettyprint lang-php">                            preg_match('#&lt;.*?Delegate&gt;(.*)&lt;/.*?Delegate&gt;#', $content, $delegate);</pre></td>
</tr>
<tr id="line430">
<td class="line-numbers"><a href="#line430" name="line430">430</a></td>
<td class="code"><pre class="prettyprint lang-php">                            if (empty($server)) {</pre></td>
</tr>
<tr id="line431">
<td class="line-numbers"><a href="#line431" name="line431">431</a></td>
<td class="code"><pre class="prettyprint lang-php">                                return false;</pre></td>
</tr>
<tr id="line432">
<td class="line-numbers"><a href="#line432" name="line432">432</a></td>
<td class="code"><pre class="prettyprint lang-php">                            }</pre></td>
</tr>
<tr id="line433">
<td class="line-numbers"><a href="#line433" name="line433">433</a></td>
<td class="code"><pre class="prettyprint lang-php">                            # AX can be used only with OpenID 2.0, so checking only SREG</pre></td>
</tr>
<tr id="line434">
<td class="line-numbers"><a href="#line434" name="line434">434</a></td>
<td class="code"><pre class="prettyprint lang-php">                            $this-&gt;sreg = strpos($content, '&lt;Type&gt;http://openid.net/sreg/1.0&lt;/Type&gt;')</pre></td>
</tr>
<tr id="line435">
<td class="line-numbers"><a href="#line435" name="line435">435</a></td>
<td class="code"><pre class="prettyprint lang-php">                                       || strpos($content, '&lt;Type&gt;http://openid.net/extensions/sreg/1.1&lt;/Type&gt;');</pre></td>
</tr>
<tr id="line436">
<td class="line-numbers"><a href="#line436" name="line436">436</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line437">
<td class="line-numbers"><a href="#line437" name="line437">437</a></td>
<td class="code"><pre class="prettyprint lang-php">                            $server = $server[1];</pre></td>
</tr>
<tr id="line438">
<td class="line-numbers"><a href="#line438" name="line438">438</a></td>
<td class="code"><pre class="prettyprint lang-php">                            if (isset($delegate[1])) $this-&gt;identity = $delegate[1];</pre></td>
</tr>
<tr id="line439">
<td class="line-numbers"><a href="#line439" name="line439">439</a></td>
<td class="code"><pre class="prettyprint lang-php">                            $this-&gt;version = 1;</pre></td>
</tr>
<tr id="line440">
<td class="line-numbers"><a href="#line440" name="line440">440</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line441">
<td class="line-numbers"><a href="#line441" name="line441">441</a></td>
<td class="code"><pre class="prettyprint lang-php">                            $this-&gt;server = $server;</pre></td>
</tr>
<tr id="line442">
<td class="line-numbers"><a href="#line442" name="line442">442</a></td>
<td class="code"><pre class="prettyprint lang-php">                            return $server;</pre></td>
</tr>
<tr id="line443">
<td class="line-numbers"><a href="#line443" name="line443">443</a></td>
<td class="code"><pre class="prettyprint lang-php">                        }</pre></td>
</tr>
<tr id="line444">
<td class="line-numbers"><a href="#line444" name="line444">444</a></td>
<td class="code"><pre class="prettyprint lang-php">                    }</pre></td>
</tr>
<tr id="line445">
<td class="line-numbers"><a href="#line445" name="line445">445</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line446">
<td class="line-numbers"><a href="#line446" name="line446">446</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $next = true;</pre></td>
</tr>
<tr id="line447">
<td class="line-numbers"><a href="#line447" name="line447">447</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $yadis = false;</pre></td>
</tr>
<tr id="line448">
<td class="line-numbers"><a href="#line448" name="line448">448</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $url = $originalUrl;</pre></td>
</tr>
<tr id="line449">
<td class="line-numbers"><a href="#line449" name="line449">449</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $content = null;</pre></td>
</tr>
<tr id="line450">
<td class="line-numbers"><a href="#line450" name="line450">450</a></td>
<td class="code"><pre class="prettyprint lang-php">                    break;</pre></td>
</tr>
<tr id="line451">
<td class="line-numbers"><a href="#line451" name="line451">451</a></td>
<td class="code"><pre class="prettyprint lang-php">                }</pre></td>
</tr>
<tr id="line452">
<td class="line-numbers"><a href="#line452" name="line452">452</a></td>
<td class="code"><pre class="prettyprint lang-php">                if ($next) continue;</pre></td>
</tr>
<tr id="line453">
<td class="line-numbers"><a href="#line453" name="line453">453</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line454">
<td class="line-numbers"><a href="#line454" name="line454">454</a></td>
<td class="code"><pre class="prettyprint lang-php">                # There are no relevant information in headers, so we search the body.</pre></td>
</tr>
<tr id="line455">
<td class="line-numbers"><a href="#line455" name="line455">455</a></td>
<td class="code"><pre class="prettyprint lang-php">                $content = $this-&gt;request($url, 'GET');</pre></td>
</tr>
<tr id="line456">
<td class="line-numbers"><a href="#line456" name="line456">456</a></td>
<td class="code"><pre class="prettyprint lang-php">                $location = $this-&gt;htmlTag($content, 'meta', 'http-equiv', 'X-XRDS-Location', 'content');</pre></td>
</tr>
<tr id="line457">
<td class="line-numbers"><a href="#line457" name="line457">457</a></td>
<td class="code"><pre class="prettyprint lang-php">                if ($location) {</pre></td>
</tr>
<tr id="line458">
<td class="line-numbers"><a href="#line458" name="line458">458</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $url = $this-&gt;build_url(parse_url($url), parse_url($location));</pre></td>
</tr>
<tr id="line459">
<td class="line-numbers"><a href="#line459" name="line459">459</a></td>
<td class="code"><pre class="prettyprint lang-php">                    continue;</pre></td>
</tr>
<tr id="line460">
<td class="line-numbers"><a href="#line460" name="line460">460</a></td>
<td class="code"><pre class="prettyprint lang-php">                }</pre></td>
</tr>
<tr id="line461">
<td class="line-numbers"><a href="#line461" name="line461">461</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line462">
<td class="line-numbers"><a href="#line462" name="line462">462</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line463">
<td class="line-numbers"><a href="#line463" name="line463">463</a></td>
<td class="code"><pre class="prettyprint lang-php">            if (!$content) $content = $this-&gt;request($url, 'GET');</pre></td>
</tr>
<tr id="line464">
<td class="line-numbers"><a href="#line464" name="line464">464</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line465">
<td class="line-numbers"><a href="#line465" name="line465">465</a></td>
<td class="code"><pre class="prettyprint lang-php">            # At this point, the YADIS Discovery has failed, so we'll switch</pre></td>
</tr>
<tr id="line466">
<td class="line-numbers"><a href="#line466" name="line466">466</a></td>
<td class="code"><pre class="prettyprint lang-php">            # to openid2 HTML discovery, then fallback to openid 1.1 discovery.</pre></td>
</tr>
<tr id="line467">
<td class="line-numbers"><a href="#line467" name="line467">467</a></td>
<td class="code"><pre class="prettyprint lang-php">            $server   = $this-&gt;htmlTag($content, 'link', 'rel', 'openid2.provider', 'href');</pre></td>
</tr>
<tr id="line468">
<td class="line-numbers"><a href="#line468" name="line468">468</a></td>
<td class="code"><pre class="prettyprint lang-php">            $delegate = $this-&gt;htmlTag($content, 'link', 'rel', 'openid2.local_id', 'href');</pre></td>
</tr>
<tr id="line469">
<td class="line-numbers"><a href="#line469" name="line469">469</a></td>
<td class="code"><pre class="prettyprint lang-php">            $this-&gt;version = 2;</pre></td>
</tr>
<tr id="line470">
<td class="line-numbers"><a href="#line470" name="line470">470</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line471">
<td class="line-numbers"><a href="#line471" name="line471">471</a></td>
<td class="code"><pre class="prettyprint lang-php">            if (!$server) {</pre></td>
</tr>
<tr id="line472">
<td class="line-numbers"><a href="#line472" name="line472">472</a></td>
<td class="code"><pre class="prettyprint lang-php">                # The same with openid 1.1</pre></td>
</tr>
<tr id="line473">
<td class="line-numbers"><a href="#line473" name="line473">473</a></td>
<td class="code"><pre class="prettyprint lang-php">                $server   = $this-&gt;htmlTag($content, 'link', 'rel', 'openid.server', 'href');</pre></td>
</tr>
<tr id="line474">
<td class="line-numbers"><a href="#line474" name="line474">474</a></td>
<td class="code"><pre class="prettyprint lang-php">                $delegate = $this-&gt;htmlTag($content, 'link', 'rel', 'openid.delegate', 'href');</pre></td>
</tr>
<tr id="line475">
<td class="line-numbers"><a href="#line475" name="line475">475</a></td>
<td class="code"><pre class="prettyprint lang-php">                $this-&gt;version = 1;</pre></td>
</tr>
<tr id="line476">
<td class="line-numbers"><a href="#line476" name="line476">476</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line477">
<td class="line-numbers"><a href="#line477" name="line477">477</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line478">
<td class="line-numbers"><a href="#line478" name="line478">478</a></td>
<td class="code"><pre class="prettyprint lang-php">            if ($server) {</pre></td>
</tr>
<tr id="line479">
<td class="line-numbers"><a href="#line479" name="line479">479</a></td>
<td class="code"><pre class="prettyprint lang-php">                # We found an OpenID2 OP Endpoint</pre></td>
</tr>
<tr id="line480">
<td class="line-numbers"><a href="#line480" name="line480">480</a></td>
<td class="code"><pre class="prettyprint lang-php">                if ($delegate) {</pre></td>
</tr>
<tr id="line481">
<td class="line-numbers"><a href="#line481" name="line481">481</a></td>
<td class="code"><pre class="prettyprint lang-php">                    # We have also found an OP-Local ID.</pre></td>
</tr>
<tr id="line482">
<td class="line-numbers"><a href="#line482" name="line482">482</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $this-&gt;identity = $delegate;</pre></td>
</tr>
<tr id="line483">
<td class="line-numbers"><a href="#line483" name="line483">483</a></td>
<td class="code"><pre class="prettyprint lang-php">                }</pre></td>
</tr>
<tr id="line484">
<td class="line-numbers"><a href="#line484" name="line484">484</a></td>
<td class="code"><pre class="prettyprint lang-php">                $this-&gt;server = $server;</pre></td>
</tr>
<tr id="line485">
<td class="line-numbers"><a href="#line485" name="line485">485</a></td>
<td class="code"><pre class="prettyprint lang-php">                return $server;</pre></td>
</tr>
<tr id="line486">
<td class="line-numbers"><a href="#line486" name="line486">486</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line487">
<td class="line-numbers"><a href="#line487" name="line487">487</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line488">
<td class="line-numbers"><a href="#line488" name="line488">488</a></td>
<td class="code"><pre class="prettyprint lang-php">            throw new ErrorException(&quot;No OpenID Server found at $url&quot;, 404);</pre></td>
</tr>
<tr id="line489">
<td class="line-numbers"><a href="#line489" name="line489">489</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line490">
<td class="line-numbers"><a href="#line490" name="line490">490</a></td>
<td class="code"><pre class="prettyprint lang-php">        throw new ErrorException('Endless redirection!', 500);</pre></td>
</tr>
<tr id="line491">
<td class="line-numbers"><a href="#line491" name="line491">491</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line492">
<td class="line-numbers"><a href="#line492" name="line492">492</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line493">
<td class="line-numbers"><a href="#line493" name="line493">493</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function sregParams()</pre></td>
</tr>
<tr id="line494">
<td class="line-numbers"><a href="#line494" name="line494">494</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line495">
<td class="line-numbers"><a href="#line495" name="line495">495</a></td>
<td class="code"><pre class="prettyprint lang-php">        $params = array();</pre></td>
</tr>
<tr id="line496">
<td class="line-numbers"><a href="#line496" name="line496">496</a></td>
<td class="code"><pre class="prettyprint lang-php">        # We always use SREG 1.1, even if the server is advertising only support for 1.0.</pre></td>
</tr>
<tr id="line497">
<td class="line-numbers"><a href="#line497" name="line497">497</a></td>
<td class="code"><pre class="prettyprint lang-php">        # That's because it's fully backwards compatibile with 1.0, and some providers</pre></td>
</tr>
<tr id="line498">
<td class="line-numbers"><a href="#line498" name="line498">498</a></td>
<td class="code"><pre class="prettyprint lang-php">        # advertise 1.0 even if they accept only 1.1. One such provider is myopenid.com</pre></td>
</tr>
<tr id="line499">
<td class="line-numbers"><a href="#line499" name="line499">499</a></td>
<td class="code"><pre class="prettyprint lang-php">        $params['openid.ns.sreg'] = 'http://openid.net/extensions/sreg/1.1';</pre></td>
</tr>
<tr id="line500">
<td class="line-numbers"><a href="#line500" name="line500">500</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ($this-&gt;required) {</pre></td>
</tr>
<tr id="line501">
<td class="line-numbers"><a href="#line501" name="line501">501</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.sreg.required'] = array();</pre></td>
</tr>
<tr id="line502">
<td class="line-numbers"><a href="#line502" name="line502">502</a></td>
<td class="code"><pre class="prettyprint lang-php">            foreach ($this-&gt;required as $required) {</pre></td>
</tr>
<tr id="line503">
<td class="line-numbers"><a href="#line503" name="line503">503</a></td>
<td class="code"><pre class="prettyprint lang-php">                if (!isset(self::$ax_to_sreg[$required])) continue;</pre></td>
</tr>
<tr id="line504">
<td class="line-numbers"><a href="#line504" name="line504">504</a></td>
<td class="code"><pre class="prettyprint lang-php">                $params['openid.sreg.required'][] = self::$ax_to_sreg[$required];</pre></td>
</tr>
<tr id="line505">
<td class="line-numbers"><a href="#line505" name="line505">505</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line506">
<td class="line-numbers"><a href="#line506" name="line506">506</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.sreg.required'] = implode(',', $params['openid.sreg.required']);</pre></td>
</tr>
<tr id="line507">
<td class="line-numbers"><a href="#line507" name="line507">507</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line508">
<td class="line-numbers"><a href="#line508" name="line508">508</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line509">
<td class="line-numbers"><a href="#line509" name="line509">509</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ($this-&gt;optional) {</pre></td>
</tr>
<tr id="line510">
<td class="line-numbers"><a href="#line510" name="line510">510</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.sreg.optional'] = array();</pre></td>
</tr>
<tr id="line511">
<td class="line-numbers"><a href="#line511" name="line511">511</a></td>
<td class="code"><pre class="prettyprint lang-php">            foreach ($this-&gt;optional as $optional) {</pre></td>
</tr>
<tr id="line512">
<td class="line-numbers"><a href="#line512" name="line512">512</a></td>
<td class="code"><pre class="prettyprint lang-php">                if (!isset(self::$ax_to_sreg[$optional])) continue;</pre></td>
</tr>
<tr id="line513">
<td class="line-numbers"><a href="#line513" name="line513">513</a></td>
<td class="code"><pre class="prettyprint lang-php">                $params['openid.sreg.optional'][] = self::$ax_to_sreg[$optional];</pre></td>
</tr>
<tr id="line514">
<td class="line-numbers"><a href="#line514" name="line514">514</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line515">
<td class="line-numbers"><a href="#line515" name="line515">515</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.sreg.optional'] = implode(',', $params['openid.sreg.optional']);</pre></td>
</tr>
<tr id="line516">
<td class="line-numbers"><a href="#line516" name="line516">516</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line517">
<td class="line-numbers"><a href="#line517" name="line517">517</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $params;</pre></td>
</tr>
<tr id="line518">
<td class="line-numbers"><a href="#line518" name="line518">518</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line519">
<td class="line-numbers"><a href="#line519" name="line519">519</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line520">
<td class="line-numbers"><a href="#line520" name="line520">520</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function axParams()</pre></td>
</tr>
<tr id="line521">
<td class="line-numbers"><a href="#line521" name="line521">521</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line522">
<td class="line-numbers"><a href="#line522" name="line522">522</a></td>
<td class="code"><pre class="prettyprint lang-php">        $params = array();</pre></td>
</tr>
<tr id="line523">
<td class="line-numbers"><a href="#line523" name="line523">523</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ($this-&gt;required || $this-&gt;optional) {</pre></td>
</tr>
<tr id="line524">
<td class="line-numbers"><a href="#line524" name="line524">524</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.ns.ax'] = 'http://openid.net/srv/ax/1.0';</pre></td>
</tr>
<tr id="line525">
<td class="line-numbers"><a href="#line525" name="line525">525</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.ax.mode'] = 'fetch_request';</pre></td>
</tr>
<tr id="line526">
<td class="line-numbers"><a href="#line526" name="line526">526</a></td>
<td class="code"><pre class="prettyprint lang-php">            $this-&gt;aliases  = array();</pre></td>
</tr>
<tr id="line527">
<td class="line-numbers"><a href="#line527" name="line527">527</a></td>
<td class="code"><pre class="prettyprint lang-php">            $counts   = array();</pre></td>
</tr>
<tr id="line528">
<td class="line-numbers"><a href="#line528" name="line528">528</a></td>
<td class="code"><pre class="prettyprint lang-php">            $required = array();</pre></td>
</tr>
<tr id="line529">
<td class="line-numbers"><a href="#line529" name="line529">529</a></td>
<td class="code"><pre class="prettyprint lang-php">            $optional = array();</pre></td>
</tr>
<tr id="line530">
<td class="line-numbers"><a href="#line530" name="line530">530</a></td>
<td class="code"><pre class="prettyprint lang-php">            foreach (array('required','optional') as $type) {</pre></td>
</tr>
<tr id="line531">
<td class="line-numbers"><a href="#line531" name="line531">531</a></td>
<td class="code"><pre class="prettyprint lang-php">                foreach ($this-&gt;$type as $alias =&gt; $field) {</pre></td>
</tr>
<tr id="line532">
<td class="line-numbers"><a href="#line532" name="line532">532</a></td>
<td class="code"><pre class="prettyprint lang-php">                    if (is_int($alias)) $alias = strtr($field, '/', '_');</pre></td>
</tr>
<tr id="line533">
<td class="line-numbers"><a href="#line533" name="line533">533</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $this-&gt;aliases[$alias] = 'http://axschema.org/' . $field;</pre></td>
</tr>
<tr id="line534">
<td class="line-numbers"><a href="#line534" name="line534">534</a></td>
<td class="code"><pre class="prettyprint lang-php">                    if (empty($counts[$alias])) $counts[$alias] = 0;</pre></td>
</tr>
<tr id="line535">
<td class="line-numbers"><a href="#line535" name="line535">535</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $counts[$alias] += 1;</pre></td>
</tr>
<tr id="line536">
<td class="line-numbers"><a href="#line536" name="line536">536</a></td>
<td class="code"><pre class="prettyprint lang-php">                    ${$type}[] = $alias;</pre></td>
</tr>
<tr id="line537">
<td class="line-numbers"><a href="#line537" name="line537">537</a></td>
<td class="code"><pre class="prettyprint lang-php">                }</pre></td>
</tr>
<tr id="line538">
<td class="line-numbers"><a href="#line538" name="line538">538</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line539">
<td class="line-numbers"><a href="#line539" name="line539">539</a></td>
<td class="code"><pre class="prettyprint lang-php">            foreach ($this-&gt;aliases as $alias =&gt; $ns) {</pre></td>
</tr>
<tr id="line540">
<td class="line-numbers"><a href="#line540" name="line540">540</a></td>
<td class="code"><pre class="prettyprint lang-php">                $params['openid.ax.type.' . $alias] = $ns;</pre></td>
</tr>
<tr id="line541">
<td class="line-numbers"><a href="#line541" name="line541">541</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line542">
<td class="line-numbers"><a href="#line542" name="line542">542</a></td>
<td class="code"><pre class="prettyprint lang-php">            foreach ($counts as $alias =&gt; $count) {</pre></td>
</tr>
<tr id="line543">
<td class="line-numbers"><a href="#line543" name="line543">543</a></td>
<td class="code"><pre class="prettyprint lang-php">                if ($count == 1) continue;</pre></td>
</tr>
<tr id="line544">
<td class="line-numbers"><a href="#line544" name="line544">544</a></td>
<td class="code"><pre class="prettyprint lang-php">                $params['openid.ax.count.' . $alias] = $count;</pre></td>
</tr>
<tr id="line545">
<td class="line-numbers"><a href="#line545" name="line545">545</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line546">
<td class="line-numbers"><a href="#line546" name="line546">546</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line547">
<td class="line-numbers"><a href="#line547" name="line547">547</a></td>
<td class="code"><pre class="prettyprint lang-php">            # Don't send empty ax.requied and ax.if_available.</pre></td>
</tr>
<tr id="line548">
<td class="line-numbers"><a href="#line548" name="line548">548</a></td>
<td class="code"><pre class="prettyprint lang-php">            # Google and possibly other providers refuse to support ax when one of these is empty.</pre></td>
</tr>
<tr id="line549">
<td class="line-numbers"><a href="#line549" name="line549">549</a></td>
<td class="code"><pre class="prettyprint lang-php">            if($required) {</pre></td>
</tr>
<tr id="line550">
<td class="line-numbers"><a href="#line550" name="line550">550</a></td>
<td class="code"><pre class="prettyprint lang-php">                $params['openid.ax.required'] = implode(',', $required);</pre></td>
</tr>
<tr id="line551">
<td class="line-numbers"><a href="#line551" name="line551">551</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line552">
<td class="line-numbers"><a href="#line552" name="line552">552</a></td>
<td class="code"><pre class="prettyprint lang-php">            if($optional) {</pre></td>
</tr>
<tr id="line553">
<td class="line-numbers"><a href="#line553" name="line553">553</a></td>
<td class="code"><pre class="prettyprint lang-php">                $params['openid.ax.if_available'] = implode(',', $optional);</pre></td>
</tr>
<tr id="line554">
<td class="line-numbers"><a href="#line554" name="line554">554</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line555">
<td class="line-numbers"><a href="#line555" name="line555">555</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line556">
<td class="line-numbers"><a href="#line556" name="line556">556</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $params;</pre></td>
</tr>
<tr id="line557">
<td class="line-numbers"><a href="#line557" name="line557">557</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line558">
<td class="line-numbers"><a href="#line558" name="line558">558</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line559">
<td class="line-numbers"><a href="#line559" name="line559">559</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function authUrl_v1($immediate)</pre></td>
</tr>
<tr id="line560">
<td class="line-numbers"><a href="#line560" name="line560">560</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line561">
<td class="line-numbers"><a href="#line561" name="line561">561</a></td>
<td class="code"><pre class="prettyprint lang-php">	$returnUrl = $this-&gt;returnUrl;</pre></td>
</tr>
<tr id="line562">
<td class="line-numbers"><a href="#line562" name="line562">562</a></td>
<td class="code"><pre class="prettyprint lang-php">        # If we have an openid.delegate that is different from our claimed id,</pre></td>
</tr>
<tr id="line563">
<td class="line-numbers"><a href="#line563" name="line563">563</a></td>
<td class="code"><pre class="prettyprint lang-php">        # we need to somehow preserve the claimed id between requests.</pre></td>
</tr>
<tr id="line564">
<td class="line-numbers"><a href="#line564" name="line564">564</a></td>
<td class="code"><pre class="prettyprint lang-php">        # The simplest way is to just send it along with the return_to url.</pre></td>
</tr>
<tr id="line565">
<td class="line-numbers"><a href="#line565" name="line565">565</a></td>
<td class="code"><pre class="prettyprint lang-php">        if($this-&gt;identity != $this-&gt;claimed_id) {</pre></td>
</tr>
<tr id="line566">
<td class="line-numbers"><a href="#line566" name="line566">566</a></td>
<td class="code"><pre class="prettyprint lang-php">            $returnUrl .= (strpos($returnUrl, '?') ? '&amp;' : '?') . 'openid.claimed_id=' . $this-&gt;claimed_id;</pre></td>
</tr>
<tr id="line567">
<td class="line-numbers"><a href="#line567" name="line567">567</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line568">
<td class="line-numbers"><a href="#line568" name="line568">568</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line569">
<td class="line-numbers"><a href="#line569" name="line569">569</a></td>
<td class="code"><pre class="prettyprint lang-php">        $params = array(</pre></td>
</tr>
<tr id="line570">
<td class="line-numbers"><a href="#line570" name="line570">570</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.return_to'  =&gt; $returnUrl,</pre></td>
</tr>
<tr id="line571">
<td class="line-numbers"><a href="#line571" name="line571">571</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.mode'       =&gt; $immediate ? 'checkid_immediate' : 'checkid_setup',</pre></td>
</tr>
<tr id="line572">
<td class="line-numbers"><a href="#line572" name="line572">572</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.identity'   =&gt; $this-&gt;identity,</pre></td>
</tr>
<tr id="line573">
<td class="line-numbers"><a href="#line573" name="line573">573</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.trust_root' =&gt; $this-&gt;trustRoot,</pre></td>
</tr>
<tr id="line574">
<td class="line-numbers"><a href="#line574" name="line574">574</a></td>
<td class="code"><pre class="prettyprint lang-php">            ) + $this-&gt;sregParams();</pre></td>
</tr>
<tr id="line575">
<td class="line-numbers"><a href="#line575" name="line575">575</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line576">
<td class="line-numbers"><a href="#line576" name="line576">576</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $this-&gt;build_url(parse_url($this-&gt;server)</pre></td>
</tr>
<tr id="line577">
<td class="line-numbers"><a href="#line577" name="line577">577</a></td>
<td class="code"><pre class="prettyprint lang-php">                               , array('query' =&gt; http_build_query($params, '', '&amp;')));</pre></td>
</tr>
<tr id="line578">
<td class="line-numbers"><a href="#line578" name="line578">578</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line579">
<td class="line-numbers"><a href="#line579" name="line579">579</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line580">
<td class="line-numbers"><a href="#line580" name="line580">580</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function authUrl_v2($immediate)</pre></td>
</tr>
<tr id="line581">
<td class="line-numbers"><a href="#line581" name="line581">581</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line582">
<td class="line-numbers"><a href="#line582" name="line582">582</a></td>
<td class="code"><pre class="prettyprint lang-php">        $params = array(</pre></td>
</tr>
<tr id="line583">
<td class="line-numbers"><a href="#line583" name="line583">583</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.ns'          =&gt; 'http://specs.openid.net/auth/2.0',</pre></td>
</tr>
<tr id="line584">
<td class="line-numbers"><a href="#line584" name="line584">584</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.mode'        =&gt; $immediate ? 'checkid_immediate' : 'checkid_setup',</pre></td>
</tr>
<tr id="line585">
<td class="line-numbers"><a href="#line585" name="line585">585</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.return_to'   =&gt; $this-&gt;returnUrl,</pre></td>
</tr>
<tr id="line586">
<td class="line-numbers"><a href="#line586" name="line586">586</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.realm'       =&gt; $this-&gt;trustRoot,</pre></td>
</tr>
<tr id="line587">
<td class="line-numbers"><a href="#line587" name="line587">587</a></td>
<td class="code"><pre class="prettyprint lang-php">        );</pre></td>
</tr>
<tr id="line588">
<td class="line-numbers"><a href="#line588" name="line588">588</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ($this-&gt;ax) {</pre></td>
</tr>
<tr id="line589">
<td class="line-numbers"><a href="#line589" name="line589">589</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params += $this-&gt;axParams();</pre></td>
</tr>
<tr id="line590">
<td class="line-numbers"><a href="#line590" name="line590">590</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line591">
<td class="line-numbers"><a href="#line591" name="line591">591</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ($this-&gt;sreg) {</pre></td>
</tr>
<tr id="line592">
<td class="line-numbers"><a href="#line592" name="line592">592</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params += $this-&gt;sregParams();</pre></td>
</tr>
<tr id="line593">
<td class="line-numbers"><a href="#line593" name="line593">593</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line594">
<td class="line-numbers"><a href="#line594" name="line594">594</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (!$this-&gt;ax &amp;&amp; !$this-&gt;sreg) {</pre></td>
</tr>
<tr id="line595">
<td class="line-numbers"><a href="#line595" name="line595">595</a></td>
<td class="code"><pre class="prettyprint lang-php">            # If OP doesn't advertise either SREG, nor AX, let's send them both</pre></td>
</tr>
<tr id="line596">
<td class="line-numbers"><a href="#line596" name="line596">596</a></td>
<td class="code"><pre class="prettyprint lang-php">            # in worst case we don't get anything in return.</pre></td>
</tr>
<tr id="line597">
<td class="line-numbers"><a href="#line597" name="line597">597</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params += $this-&gt;axParams() + $this-&gt;sregParams();</pre></td>
</tr>
<tr id="line598">
<td class="line-numbers"><a href="#line598" name="line598">598</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line599">
<td class="line-numbers"><a href="#line599" name="line599">599</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line600">
<td class="line-numbers"><a href="#line600" name="line600">600</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ($this-&gt;identifier_select) {</pre></td>
</tr>
<tr id="line601">
<td class="line-numbers"><a href="#line601" name="line601">601</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.identity'] = $params['openid.claimed_id']</pre></td>
</tr>
<tr id="line602">
<td class="line-numbers"><a href="#line602" name="line602">602</a></td>
<td class="code"><pre class="prettyprint lang-php">                 = 'http://specs.openid.net/auth/2.0/identifier_select';</pre></td>
</tr>
<tr id="line603">
<td class="line-numbers"><a href="#line603" name="line603">603</a></td>
<td class="code"><pre class="prettyprint lang-php">        } else {</pre></td>
</tr>
<tr id="line604">
<td class="line-numbers"><a href="#line604" name="line604">604</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.identity'] = $this-&gt;identity;</pre></td>
</tr>
<tr id="line605">
<td class="line-numbers"><a href="#line605" name="line605">605</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.claimed_id'] = $this-&gt;claimed_id;</pre></td>
</tr>
<tr id="line606">
<td class="line-numbers"><a href="#line606" name="line606">606</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line607">
<td class="line-numbers"><a href="#line607" name="line607">607</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line608">
<td class="line-numbers"><a href="#line608" name="line608">608</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $this-&gt;build_url(parse_url($this-&gt;server)</pre></td>
</tr>
<tr id="line609">
<td class="line-numbers"><a href="#line609" name="line609">609</a></td>
<td class="code"><pre class="prettyprint lang-php">                               , array('query' =&gt; http_build_query($params, '', '&amp;')));</pre></td>
</tr>
<tr id="line610">
<td class="line-numbers"><a href="#line610" name="line610">610</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line611">
<td class="line-numbers"><a href="#line611" name="line611">611</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line612">
<td class="line-numbers"><a href="#line612" name="line612">612</a></td>
<td class="code"><pre class="prettyprint lang-php">    /**</pre></td>
</tr>
<tr id="line613">
<td class="line-numbers"><a href="#line613" name="line613">613</a></td>
<td class="code"><pre class="prettyprint lang-php">     * Returns authentication url. Usually, you want to redirect your user to it.</pre></td>
</tr>
<tr id="line614">
<td class="line-numbers"><a href="#line614" name="line614">614</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @return String The authentication url.</pre></td>
</tr>
<tr id="line615">
<td class="line-numbers"><a href="#line615" name="line615">615</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @param String $select_identifier Whether to request OP to select identity for an user in OpenID 2. Does not affect OpenID 1.</pre></td>
</tr>
<tr id="line616">
<td class="line-numbers"><a href="#line616" name="line616">616</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @throws ErrorException</pre></td>
</tr>
<tr id="line617">
<td class="line-numbers"><a href="#line617" name="line617">617</a></td>
<td class="code"><pre class="prettyprint lang-php">     */</pre></td>
</tr>
<tr id="line618">
<td class="line-numbers"><a href="#line618" name="line618">618</a></td>
<td class="code"><pre class="prettyprint lang-php">    function authUrl($immediate = false)</pre></td>
</tr>
<tr id="line619">
<td class="line-numbers"><a href="#line619" name="line619">619</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line620">
<td class="line-numbers"><a href="#line620" name="line620">620</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ($this-&gt;setup_url &amp;&amp; !$immediate) return $this-&gt;setup_url;</pre></td>
</tr>
<tr id="line621">
<td class="line-numbers"><a href="#line621" name="line621">621</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (!$this-&gt;server) $this-&gt;discover($this-&gt;identity);</pre></td>
</tr>
<tr id="line622">
<td class="line-numbers"><a href="#line622" name="line622">622</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line623">
<td class="line-numbers"><a href="#line623" name="line623">623</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ($this-&gt;version == 2) {</pre></td>
</tr>
<tr id="line624">
<td class="line-numbers"><a href="#line624" name="line624">624</a></td>
<td class="code"><pre class="prettyprint lang-php">            return $this-&gt;authUrl_v2($immediate);</pre></td>
</tr>
<tr id="line625">
<td class="line-numbers"><a href="#line625" name="line625">625</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line626">
<td class="line-numbers"><a href="#line626" name="line626">626</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $this-&gt;authUrl_v1($immediate);</pre></td>
</tr>
<tr id="line627">
<td class="line-numbers"><a href="#line627" name="line627">627</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line628">
<td class="line-numbers"><a href="#line628" name="line628">628</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line629">
<td class="line-numbers"><a href="#line629" name="line629">629</a></td>
<td class="code"><pre class="prettyprint lang-php">    /**</pre></td>
</tr>
<tr id="line630">
<td class="line-numbers"><a href="#line630" name="line630">630</a></td>
<td class="code"><pre class="prettyprint lang-php">     * Performs OpenID verification with the OP.</pre></td>
</tr>
<tr id="line631">
<td class="line-numbers"><a href="#line631" name="line631">631</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @return Bool Whether the verification was successful.</pre></td>
</tr>
<tr id="line632">
<td class="line-numbers"><a href="#line632" name="line632">632</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @throws ErrorException</pre></td>
</tr>
<tr id="line633">
<td class="line-numbers"><a href="#line633" name="line633">633</a></td>
<td class="code"><pre class="prettyprint lang-php">     */</pre></td>
</tr>
<tr id="line634">
<td class="line-numbers"><a href="#line634" name="line634">634</a></td>
<td class="code"><pre class="prettyprint lang-php">    function validate()</pre></td>
</tr>
<tr id="line635">
<td class="line-numbers"><a href="#line635" name="line635">635</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line636">
<td class="line-numbers"><a href="#line636" name="line636">636</a></td>
<td class="code"><pre class="prettyprint lang-php">        # If the request was using immediate mode, a failure may be reported</pre></td>
</tr>
<tr id="line637">
<td class="line-numbers"><a href="#line637" name="line637">637</a></td>
<td class="code"><pre class="prettyprint lang-php">        # by presenting user_setup_url (for 1.1) or reporting</pre></td>
</tr>
<tr id="line638">
<td class="line-numbers"><a href="#line638" name="line638">638</a></td>
<td class="code"><pre class="prettyprint lang-php">        # mode 'setup_needed' (for 2.0). Also catching all modes other than</pre></td>
</tr>
<tr id="line639">
<td class="line-numbers"><a href="#line639" name="line639">639</a></td>
<td class="code"><pre class="prettyprint lang-php">        # id_res, in order to avoid throwing errors.</pre></td>
</tr>
<tr id="line640">
<td class="line-numbers"><a href="#line640" name="line640">640</a></td>
<td class="code"><pre class="prettyprint lang-php">        if(isset($this-&gt;data['openid_user_setup_url'])) {</pre></td>
</tr>
<tr id="line641">
<td class="line-numbers"><a href="#line641" name="line641">641</a></td>
<td class="code"><pre class="prettyprint lang-php">            $this-&gt;setup_url = $this-&gt;data['openid_user_setup_url'];</pre></td>
</tr>
<tr id="line642">
<td class="line-numbers"><a href="#line642" name="line642">642</a></td>
<td class="code"><pre class="prettyprint lang-php">            return false;</pre></td>
</tr>
<tr id="line643">
<td class="line-numbers"><a href="#line643" name="line643">643</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line644">
<td class="line-numbers"><a href="#line644" name="line644">644</a></td>
<td class="code"><pre class="prettyprint lang-php">        if($this-&gt;mode != 'id_res') {</pre></td>
</tr>
<tr id="line645">
<td class="line-numbers"><a href="#line645" name="line645">645</a></td>
<td class="code"><pre class="prettyprint lang-php">            return false;</pre></td>
</tr>
<tr id="line646">
<td class="line-numbers"><a href="#line646" name="line646">646</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line647">
<td class="line-numbers"><a href="#line647" name="line647">647</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line648">
<td class="line-numbers"><a href="#line648" name="line648">648</a></td>
<td class="code"><pre class="prettyprint lang-php">        $this-&gt;claimed_id = isset($this-&gt;data['openid_claimed_id'])?$this-&gt;data['openid_claimed_id']:$this-&gt;data['openid_identity'];</pre></td>
</tr>
<tr id="line649">
<td class="line-numbers"><a href="#line649" name="line649">649</a></td>
<td class="code"><pre class="prettyprint lang-php">        $params = array(</pre></td>
</tr>
<tr id="line650">
<td class="line-numbers"><a href="#line650" name="line650">650</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.assoc_handle' =&gt; $this-&gt;data['openid_assoc_handle'],</pre></td>
</tr>
<tr id="line651">
<td class="line-numbers"><a href="#line651" name="line651">651</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.signed'       =&gt; $this-&gt;data['openid_signed'],</pre></td>
</tr>
<tr id="line652">
<td class="line-numbers"><a href="#line652" name="line652">652</a></td>
<td class="code"><pre class="prettyprint lang-php">            'openid.sig'          =&gt; $this-&gt;data['openid_sig'],</pre></td>
</tr>
<tr id="line653">
<td class="line-numbers"><a href="#line653" name="line653">653</a></td>
<td class="code"><pre class="prettyprint lang-php">            );</pre></td>
</tr>
<tr id="line654">
<td class="line-numbers"><a href="#line654" name="line654">654</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line655">
<td class="line-numbers"><a href="#line655" name="line655">655</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (isset($this-&gt;data['openid_ns'])) {</pre></td>
</tr>
<tr id="line656">
<td class="line-numbers"><a href="#line656" name="line656">656</a></td>
<td class="code"><pre class="prettyprint lang-php">            # We're dealing with an OpenID 2.0 server, so let's set an ns</pre></td>
</tr>
<tr id="line657">
<td class="line-numbers"><a href="#line657" name="line657">657</a></td>
<td class="code"><pre class="prettyprint lang-php">            # Even though we should know location of the endpoint,</pre></td>
</tr>
<tr id="line658">
<td class="line-numbers"><a href="#line658" name="line658">658</a></td>
<td class="code"><pre class="prettyprint lang-php">            # we still need to verify it by discovery, so $server is not set here</pre></td>
</tr>
<tr id="line659">
<td class="line-numbers"><a href="#line659" name="line659">659</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.ns'] = 'http://specs.openid.net/auth/2.0';</pre></td>
</tr>
<tr id="line660">
<td class="line-numbers"><a href="#line660" name="line660">660</a></td>
<td class="code"><pre class="prettyprint lang-php">        } elseif (isset($this-&gt;data['openid_claimed_id'])</pre></td>
</tr>
<tr id="line661">
<td class="line-numbers"><a href="#line661" name="line661">661</a></td>
<td class="code"><pre class="prettyprint lang-php">            &amp;&amp; $this-&gt;data['openid_claimed_id'] != $this-&gt;data['openid_identity']</pre></td>
</tr>
<tr id="line662">
<td class="line-numbers"><a href="#line662" name="line662">662</a></td>
<td class="code"><pre class="prettyprint lang-php">        ) {</pre></td>
</tr>
<tr id="line663">
<td class="line-numbers"><a href="#line663" name="line663">663</a></td>
<td class="code"><pre class="prettyprint lang-php">            # If it's an OpenID 1 provider, and we've got claimed_id,</pre></td>
</tr>
<tr id="line664">
<td class="line-numbers"><a href="#line664" name="line664">664</a></td>
<td class="code"><pre class="prettyprint lang-php">            # we have to append it to the returnUrl, like authUrl_v1 does.</pre></td>
</tr>
<tr id="line665">
<td class="line-numbers"><a href="#line665" name="line665">665</a></td>
<td class="code"><pre class="prettyprint lang-php">            $this-&gt;returnUrl .= (strpos($this-&gt;returnUrl, '?') ? '&amp;' : '?')</pre></td>
</tr>
<tr id="line666">
<td class="line-numbers"><a href="#line666" name="line666">666</a></td>
<td class="code"><pre class="prettyprint lang-php">                             .  'openid.claimed_id=' . $this-&gt;claimed_id;</pre></td>
</tr>
<tr id="line667">
<td class="line-numbers"><a href="#line667" name="line667">667</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line668">
<td class="line-numbers"><a href="#line668" name="line668">668</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line669">
<td class="line-numbers"><a href="#line669" name="line669">669</a></td>
<td class="code"><pre class="prettyprint lang-php">        if ($this-&gt;data['openid_return_to'] != $this-&gt;returnUrl) {</pre></td>
</tr>
<tr id="line670">
<td class="line-numbers"><a href="#line670" name="line670">670</a></td>
<td class="code"><pre class="prettyprint lang-php">            # The return_to url must match the url of current request.</pre></td>
</tr>
<tr id="line671">
<td class="line-numbers"><a href="#line671" name="line671">671</a></td>
<td class="code"><pre class="prettyprint lang-php">            # I'm assuing that noone will set the returnUrl to something that doesn't make sense.</pre></td>
</tr>
<tr id="line672">
<td class="line-numbers"><a href="#line672" name="line672">672</a></td>
<td class="code"><pre class="prettyprint lang-php">            return false;</pre></td>
</tr>
<tr id="line673">
<td class="line-numbers"><a href="#line673" name="line673">673</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line674">
<td class="line-numbers"><a href="#line674" name="line674">674</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line675">
<td class="line-numbers"><a href="#line675" name="line675">675</a></td>
<td class="code"><pre class="prettyprint lang-php">        $server = $this-&gt;discover($this-&gt;claimed_id);</pre></td>
</tr>
<tr id="line676">
<td class="line-numbers"><a href="#line676" name="line676">676</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line677">
<td class="line-numbers"><a href="#line677" name="line677">677</a></td>
<td class="code"><pre class="prettyprint lang-php">        foreach (explode(',', $this-&gt;data['openid_signed']) as $item) {</pre></td>
</tr>
<tr id="line678">
<td class="line-numbers"><a href="#line678" name="line678">678</a></td>
<td class="code"><pre class="prettyprint lang-php">            # Checking whether magic_quotes_gpc is turned on, because</pre></td>
</tr>
<tr id="line679">
<td class="line-numbers"><a href="#line679" name="line679">679</a></td>
<td class="code"><pre class="prettyprint lang-php">            # the function may fail if it is. For example, when fetching</pre></td>
</tr>
<tr id="line680">
<td class="line-numbers"><a href="#line680" name="line680">680</a></td>
<td class="code"><pre class="prettyprint lang-php">            # AX namePerson, it might containg an apostrophe, which will be escaped.</pre></td>
</tr>
<tr id="line681">
<td class="line-numbers"><a href="#line681" name="line681">681</a></td>
<td class="code"><pre class="prettyprint lang-php">            # In such case, validation would fail, since we'd send different data than OP</pre></td>
</tr>
<tr id="line682">
<td class="line-numbers"><a href="#line682" name="line682">682</a></td>
<td class="code"><pre class="prettyprint lang-php">            # wants to verify. stripslashes() should solve that problem, but we can't</pre></td>
</tr>
<tr id="line683">
<td class="line-numbers"><a href="#line683" name="line683">683</a></td>
<td class="code"><pre class="prettyprint lang-php">            # use it when magic_quotes is off.</pre></td>
</tr>
<tr id="line684">
<td class="line-numbers"><a href="#line684" name="line684">684</a></td>
<td class="code"><pre class="prettyprint lang-php">            $value = $this-&gt;data['openid_' . str_replace('.','_',$item)];</pre></td>
</tr>
<tr id="line685">
<td class="line-numbers"><a href="#line685" name="line685">685</a></td>
<td class="code"><pre class="prettyprint lang-php">            $params['openid.' . $item] = get_magic_quotes_gpc() ? stripslashes($value) : $value;</pre></td>
</tr>
<tr id="line686">
<td class="line-numbers"><a href="#line686" name="line686">686</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line687">
<td class="line-numbers"><a href="#line687" name="line687">687</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line688">
<td class="line-numbers"><a href="#line688" name="line688">688</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line689">
<td class="line-numbers"><a href="#line689" name="line689">689</a></td>
<td class="code"><pre class="prettyprint lang-php">        $params['openid.mode'] = 'check_authentication';</pre></td>
</tr>
<tr id="line690">
<td class="line-numbers"><a href="#line690" name="line690">690</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line691">
<td class="line-numbers"><a href="#line691" name="line691">691</a></td>
<td class="code"><pre class="prettyprint lang-php">        $response = $this-&gt;request($server, 'POST', $params);</pre></td>
</tr>
<tr id="line692">
<td class="line-numbers"><a href="#line692" name="line692">692</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line693">
<td class="line-numbers"><a href="#line693" name="line693">693</a></td>
<td class="code"><pre class="prettyprint lang-php">        return preg_match('/is_valid\s*:\s*true/i', $response);</pre></td>
</tr>
<tr id="line694">
<td class="line-numbers"><a href="#line694" name="line694">694</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line695">
<td class="line-numbers"><a href="#line695" name="line695">695</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line696">
<td class="line-numbers"><a href="#line696" name="line696">696</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function getAxAttributes()</pre></td>
</tr>
<tr id="line697">
<td class="line-numbers"><a href="#line697" name="line697">697</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line698">
<td class="line-numbers"><a href="#line698" name="line698">698</a></td>
<td class="code"><pre class="prettyprint lang-php">        $alias = null;</pre></td>
</tr>
<tr id="line699">
<td class="line-numbers"><a href="#line699" name="line699">699</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (isset($this-&gt;data['openid_ns_ax'])</pre></td>
</tr>
<tr id="line700">
<td class="line-numbers"><a href="#line700" name="line700">700</a></td>
<td class="code"><pre class="prettyprint lang-php">            &amp;&amp; $this-&gt;data['openid_ns_ax'] != 'http://openid.net/srv/ax/1.0'</pre></td>
</tr>
<tr id="line701">
<td class="line-numbers"><a href="#line701" name="line701">701</a></td>
<td class="code"><pre class="prettyprint lang-php">        ) { # It's the most likely case, so we'll check it before</pre></td>
</tr>
<tr id="line702">
<td class="line-numbers"><a href="#line702" name="line702">702</a></td>
<td class="code"><pre class="prettyprint lang-php">            $alias = 'ax';</pre></td>
</tr>
<tr id="line703">
<td class="line-numbers"><a href="#line703" name="line703">703</a></td>
<td class="code"><pre class="prettyprint lang-php">        } else {</pre></td>
</tr>
<tr id="line704">
<td class="line-numbers"><a href="#line704" name="line704">704</a></td>
<td class="code"><pre class="prettyprint lang-php">            # 'ax' prefix is either undefined, or points to another extension,</pre></td>
</tr>
<tr id="line705">
<td class="line-numbers"><a href="#line705" name="line705">705</a></td>
<td class="code"><pre class="prettyprint lang-php">            # so we search for another prefix</pre></td>
</tr>
<tr id="line706">
<td class="line-numbers"><a href="#line706" name="line706">706</a></td>
<td class="code"><pre class="prettyprint lang-php">            foreach ($this-&gt;data as $key =&gt; $val) {</pre></td>
</tr>
<tr id="line707">
<td class="line-numbers"><a href="#line707" name="line707">707</a></td>
<td class="code"><pre class="prettyprint lang-php">                if (substr($key, 0, strlen('openid_ns_')) == 'openid_ns_'</pre></td>
</tr>
<tr id="line708">
<td class="line-numbers"><a href="#line708" name="line708">708</a></td>
<td class="code"><pre class="prettyprint lang-php">                    &amp;&amp; $val == 'http://openid.net/srv/ax/1.0'</pre></td>
</tr>
<tr id="line709">
<td class="line-numbers"><a href="#line709" name="line709">709</a></td>
<td class="code"><pre class="prettyprint lang-php">                ) {</pre></td>
</tr>
<tr id="line710">
<td class="line-numbers"><a href="#line710" name="line710">710</a></td>
<td class="code"><pre class="prettyprint lang-php">                    $alias = substr($key, strlen('openid_ns_'));</pre></td>
</tr>
<tr id="line711">
<td class="line-numbers"><a href="#line711" name="line711">711</a></td>
<td class="code"><pre class="prettyprint lang-php">                    break;</pre></td>
</tr>
<tr id="line712">
<td class="line-numbers"><a href="#line712" name="line712">712</a></td>
<td class="code"><pre class="prettyprint lang-php">                }</pre></td>
</tr>
<tr id="line713">
<td class="line-numbers"><a href="#line713" name="line713">713</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line714">
<td class="line-numbers"><a href="#line714" name="line714">714</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line715">
<td class="line-numbers"><a href="#line715" name="line715">715</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (!$alias) {</pre></td>
</tr>
<tr id="line716">
<td class="line-numbers"><a href="#line716" name="line716">716</a></td>
<td class="code"><pre class="prettyprint lang-php">            # An alias for AX schema has not been found,</pre></td>
</tr>
<tr id="line717">
<td class="line-numbers"><a href="#line717" name="line717">717</a></td>
<td class="code"><pre class="prettyprint lang-php">            # so there is no AX data in the OP's response</pre></td>
</tr>
<tr id="line718">
<td class="line-numbers"><a href="#line718" name="line718">718</a></td>
<td class="code"><pre class="prettyprint lang-php">            return array();</pre></td>
</tr>
<tr id="line719">
<td class="line-numbers"><a href="#line719" name="line719">719</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line720">
<td class="line-numbers"><a href="#line720" name="line720">720</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line721">
<td class="line-numbers"><a href="#line721" name="line721">721</a></td>
<td class="code"><pre class="prettyprint lang-php">        $attributes = array();</pre></td>
</tr>
<tr id="line722">
<td class="line-numbers"><a href="#line722" name="line722">722</a></td>
<td class="code"><pre class="prettyprint lang-php">        foreach (explode(',', $this-&gt;data['openid_signed']) as $key) {</pre></td>
</tr>
<tr id="line723">
<td class="line-numbers"><a href="#line723" name="line723">723</a></td>
<td class="code"><pre class="prettyprint lang-php">            $keyMatch = $alias . '.value.';</pre></td>
</tr>
<tr id="line724">
<td class="line-numbers"><a href="#line724" name="line724">724</a></td>
<td class="code"><pre class="prettyprint lang-php">            if (substr($key, 0, strlen($keyMatch)) != $keyMatch) {</pre></td>
</tr>
<tr id="line725">
<td class="line-numbers"><a href="#line725" name="line725">725</a></td>
<td class="code"><pre class="prettyprint lang-php">                continue;</pre></td>
</tr>
<tr id="line726">
<td class="line-numbers"><a href="#line726" name="line726">726</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line727">
<td class="line-numbers"><a href="#line727" name="line727">727</a></td>
<td class="code"><pre class="prettyprint lang-php">            $key = substr($key, strlen($keyMatch));</pre></td>
</tr>
<tr id="line728">
<td class="line-numbers"><a href="#line728" name="line728">728</a></td>
<td class="code"><pre class="prettyprint lang-php">            if (!isset($this-&gt;data['openid_' . $alias . '_type_' . $key])) {</pre></td>
</tr>
<tr id="line729">
<td class="line-numbers"><a href="#line729" name="line729">729</a></td>
<td class="code"><pre class="prettyprint lang-php">                # OP is breaking the spec by returning a field without</pre></td>
</tr>
<tr id="line730">
<td class="line-numbers"><a href="#line730" name="line730">730</a></td>
<td class="code"><pre class="prettyprint lang-php">                # associated ns. This shouldn't happen, but it's better</pre></td>
</tr>
<tr id="line731">
<td class="line-numbers"><a href="#line731" name="line731">731</a></td>
<td class="code"><pre class="prettyprint lang-php">                # to check, than cause an E_NOTICE.</pre></td>
</tr>
<tr id="line732">
<td class="line-numbers"><a href="#line732" name="line732">732</a></td>
<td class="code"><pre class="prettyprint lang-php">                continue;</pre></td>
</tr>
<tr id="line733">
<td class="line-numbers"><a href="#line733" name="line733">733</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line734">
<td class="line-numbers"><a href="#line734" name="line734">734</a></td>
<td class="code"><pre class="prettyprint lang-php">            $value = $this-&gt;data['openid_' . $alias . '_value_' . $key];</pre></td>
</tr>
<tr id="line735">
<td class="line-numbers"><a href="#line735" name="line735">735</a></td>
<td class="code"><pre class="prettyprint lang-php">            $key = substr($this-&gt;data['openid_' . $alias . '_type_' . $key],</pre></td>
</tr>
<tr id="line736">
<td class="line-numbers"><a href="#line736" name="line736">736</a></td>
<td class="code"><pre class="prettyprint lang-php">                          strlen('http://axschema.org/'));</pre></td>
</tr>
<tr id="line737">
<td class="line-numbers"><a href="#line737" name="line737">737</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line738">
<td class="line-numbers"><a href="#line738" name="line738">738</a></td>
<td class="code"><pre class="prettyprint lang-php">            $attributes[$key] = $value;</pre></td>
</tr>
<tr id="line739">
<td class="line-numbers"><a href="#line739" name="line739">739</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line740">
<td class="line-numbers"><a href="#line740" name="line740">740</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $attributes;</pre></td>
</tr>
<tr id="line741">
<td class="line-numbers"><a href="#line741" name="line741">741</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line742">
<td class="line-numbers"><a href="#line742" name="line742">742</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line743">
<td class="line-numbers"><a href="#line743" name="line743">743</a></td>
<td class="code"><pre class="prettyprint lang-php">    protected function getSregAttributes()</pre></td>
</tr>
<tr id="line744">
<td class="line-numbers"><a href="#line744" name="line744">744</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line745">
<td class="line-numbers"><a href="#line745" name="line745">745</a></td>
<td class="code"><pre class="prettyprint lang-php">        $attributes = array();</pre></td>
</tr>
<tr id="line746">
<td class="line-numbers"><a href="#line746" name="line746">746</a></td>
<td class="code"><pre class="prettyprint lang-php">        $sreg_to_ax = array_flip(self::$ax_to_sreg);</pre></td>
</tr>
<tr id="line747">
<td class="line-numbers"><a href="#line747" name="line747">747</a></td>
<td class="code"><pre class="prettyprint lang-php">        foreach (explode(',', $this-&gt;data['openid_signed']) as $key) {</pre></td>
</tr>
<tr id="line748">
<td class="line-numbers"><a href="#line748" name="line748">748</a></td>
<td class="code"><pre class="prettyprint lang-php">            $keyMatch = 'sreg.';</pre></td>
</tr>
<tr id="line749">
<td class="line-numbers"><a href="#line749" name="line749">749</a></td>
<td class="code"><pre class="prettyprint lang-php">            if (substr($key, 0, strlen($keyMatch)) != $keyMatch) {</pre></td>
</tr>
<tr id="line750">
<td class="line-numbers"><a href="#line750" name="line750">750</a></td>
<td class="code"><pre class="prettyprint lang-php">                continue;</pre></td>
</tr>
<tr id="line751">
<td class="line-numbers"><a href="#line751" name="line751">751</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line752">
<td class="line-numbers"><a href="#line752" name="line752">752</a></td>
<td class="code"><pre class="prettyprint lang-php">            $key = substr($key, strlen($keyMatch));</pre></td>
</tr>
<tr id="line753">
<td class="line-numbers"><a href="#line753" name="line753">753</a></td>
<td class="code"><pre class="prettyprint lang-php">            if (!isset($sreg_to_ax[$key])) {</pre></td>
</tr>
<tr id="line754">
<td class="line-numbers"><a href="#line754" name="line754">754</a></td>
<td class="code"><pre class="prettyprint lang-php">                # The field name isn't part of the SREG spec, so we ignore it.</pre></td>
</tr>
<tr id="line755">
<td class="line-numbers"><a href="#line755" name="line755">755</a></td>
<td class="code"><pre class="prettyprint lang-php">                continue;</pre></td>
</tr>
<tr id="line756">
<td class="line-numbers"><a href="#line756" name="line756">756</a></td>
<td class="code"><pre class="prettyprint lang-php">            }</pre></td>
</tr>
<tr id="line757">
<td class="line-numbers"><a href="#line757" name="line757">757</a></td>
<td class="code"><pre class="prettyprint lang-php">            $attributes[$sreg_to_ax[$key]] = $this-&gt;data['openid_sreg_' . $key];</pre></td>
</tr>
<tr id="line758">
<td class="line-numbers"><a href="#line758" name="line758">758</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line759">
<td class="line-numbers"><a href="#line759" name="line759">759</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $attributes;</pre></td>
</tr>
<tr id="line760">
<td class="line-numbers"><a href="#line760" name="line760">760</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line761">
<td class="line-numbers"><a href="#line761" name="line761">761</a></td>
<td class="code"><pre class="prettyprint lang-php"></pre></td>
</tr>
<tr id="line762">
<td class="line-numbers"><a href="#line762" name="line762">762</a></td>
<td class="code"><pre class="prettyprint lang-php">    /**</pre></td>
</tr>
<tr id="line763">
<td class="line-numbers"><a href="#line763" name="line763">763</a></td>
<td class="code"><pre class="prettyprint lang-php">     * Gets AX/SREG attributes provided by OP. should be used only after successful validaton.</pre></td>
</tr>
<tr id="line764">
<td class="line-numbers"><a href="#line764" name="line764">764</a></td>
<td class="code"><pre class="prettyprint lang-php">     * Note that it does not guarantee that any of the required/optional parameters will be present,</pre></td>
</tr>
<tr id="line765">
<td class="line-numbers"><a href="#line765" name="line765">765</a></td>
<td class="code"><pre class="prettyprint lang-php">     * or that there will be no other attributes besides those specified.</pre></td>
</tr>
<tr id="line766">
<td class="line-numbers"><a href="#line766" name="line766">766</a></td>
<td class="code"><pre class="prettyprint lang-php">     * In other words. OP may provide whatever information it wants to.</pre></td>
</tr>
<tr id="line767">
<td class="line-numbers"><a href="#line767" name="line767">767</a></td>
<td class="code"><pre class="prettyprint lang-php">     *     * SREG names will be mapped to AX names.</pre></td>
</tr>
<tr id="line768">
<td class="line-numbers"><a href="#line768" name="line768">768</a></td>
<td class="code"><pre class="prettyprint lang-php">     *     * @return Array Array of attributes with keys being the AX schema names, e.g. 'contact/email'</pre></td>
</tr>
<tr id="line769">
<td class="line-numbers"><a href="#line769" name="line769">769</a></td>
<td class="code"><pre class="prettyprint lang-php">     * @see http://www.axschema.org/types/</pre></td>
</tr>
<tr id="line770">
<td class="line-numbers"><a href="#line770" name="line770">770</a></td>
<td class="code"><pre class="prettyprint lang-php">     */</pre></td>
</tr>
<tr id="line771">
<td class="line-numbers"><a href="#line771" name="line771">771</a></td>
<td class="code"><pre class="prettyprint lang-php">    function getAttributes()</pre></td>
</tr>
<tr id="line772">
<td class="line-numbers"><a href="#line772" name="line772">772</a></td>
<td class="code"><pre class="prettyprint lang-php">    {</pre></td>
</tr>
<tr id="line773">
<td class="line-numbers"><a href="#line773" name="line773">773</a></td>
<td class="code"><pre class="prettyprint lang-php">        if (isset($this-&gt;data['openid_ns'])</pre></td>
</tr>
<tr id="line774">
<td class="line-numbers"><a href="#line774" name="line774">774</a></td>
<td class="code"><pre class="prettyprint lang-php">            &amp;&amp; $this-&gt;data['openid_ns'] == 'http://specs.openid.net/auth/2.0'</pre></td>
</tr>
<tr id="line775">
<td class="line-numbers"><a href="#line775" name="line775">775</a></td>
<td class="code"><pre class="prettyprint lang-php">        ) { # OpenID 2.0</pre></td>
</tr>
<tr id="line776">
<td class="line-numbers"><a href="#line776" name="line776">776</a></td>
<td class="code"><pre class="prettyprint lang-php">            # We search for both AX and SREG attributes, with AX taking precedence.</pre></td>
</tr>
<tr id="line777">
<td class="line-numbers"><a href="#line777" name="line777">777</a></td>
<td class="code"><pre class="prettyprint lang-php">            return $this-&gt;getAxAttributes() + $this-&gt;getSregAttributes();</pre></td>
</tr>
<tr id="line778">
<td class="line-numbers"><a href="#line778" name="line778">778</a></td>
<td class="code"><pre class="prettyprint lang-php">        }</pre></td>
</tr>
<tr id="line779">
<td class="line-numbers"><a href="#line779" name="line779">779</a></td>
<td class="code"><pre class="prettyprint lang-php">        return $this-&gt;getSregAttributes();</pre></td>
</tr>
<tr id="line780">
<td class="line-numbers"><a href="#line780" name="line780">780</a></td>
<td class="code"><pre class="prettyprint lang-php">    }</pre></td>
</tr>
<tr id="line781">
<td class="line-numbers"><a href="#line781" name="line781">781</a></td>
<td class="code"><pre class="prettyprint lang-php">}</pre></td>
</tr>
</table>  
      </div>
          </div>
    <div id="footer">
      
<div class="powered-by">
  <a href="http://gitorious.org"><img alt="Poweredby" src="/images/../img/poweredby.png?1294322727" title="Powered by Gitorious" /></a></div>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-52238-3']);
_gaq.push(['_setDomainName', '.gitorious.org'])
_gaq.push(['_trackPageview']);
(function() {
   var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
   ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
   (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
})();
</script><script src="/javascripts/onload.js?1317147383" type="text/javascript"></script>
      <div id="footer-links">
	<h3>Gitorious</h3>
	<ul>
	  <li><a href="/">Home</a></li>
	  <li><a href="/about">About</a></li>
	  <li><a href="/about/faq">FAQ</a></li>
	  <li><a href="/contact">Contact</a></li>
	</ul>
	<ul>
	  <li><a href="http://groups.google.com/group/gitorious">Discussion group</a></li>
	  <li><a href="http://blog.gitorious.org">Blog</a></li>
	</ul>
		  <ul>
	    <li><a href="http://en.gitorious.org/tos">Terms of Service</a></li>
            <li><a href="http://en.gitorious.org/privacy_policy">Privacy Policy</a></li>
	  </ul>
	
      </div>
      <p class="footer-blurb">
  
    <a href="http://gitorious.com">Professional Gitorious services</a> - Git
    hosting at your company, custom features, support and more.
    <a href="http://gitorious.com">gitorious.com</a>.
  
</p>

      <div class="clear"></div>
    </div>
  </div>
</body>
</html>
