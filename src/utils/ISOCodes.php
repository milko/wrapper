<?php

/**
 * ISOCodes.php
 *
 * This file contains the definition of the {@link ISOCodes} class.
 */

namespace Milko\utils;

/*=======================================================================================
 *																						*
 *									ISOCodes.php										*
 *																						*
 *======================================================================================*/

/**
 * <h4>ISO codes loader.</h4><p />
 *
 * This <em>utility</em> class can be used to compile a collection of Json files containing
 * various ISO standards from the {@link https://pkg-isocodes.alioth.debian.org} repository;
 * you must have a local copy.
 *
 * The standards supported by this class are:
 *
 * <ul>
 *  <li><b>639-2</b>: ISO 639-2 language codes.
 *  <li><b>639-3</b>: ISO 639-3 language codes.
 *  <li><b>639-5</b>: ISO 639-5 language family and groups codes.
 *  <li><b>3166-1</b>: ISO 3166-1 country codes.
 *  <li><b>3166-2</b>: ISO 3166-2 country and subdivision codes.
 *  <li><b>3166-3</b>: ISO 3166-3 formerly used country codes.
 *  <li><b>4217</b>: ISO 4217 currency codes.
 *  <li><b>15924</b>: ISO 15924 codes for the representation of names of scripts.
 * </ul>
 *
 * The class expects a directory containing Json files: file names prefixed with
 * <tt>iso_</tt> contain the codes, file names prefixed with <tt>schema-</tt> contain the
 * codes file schema.
 *
 * The language translations should be in a directory containing a set of directories whose
 * names are prefixed with <tt>iso_</tt> containing the <tt>.po</tt> files.
 *
 * The {@link getIterator()} method can be invoked with a specific standard and will return
 * an iterator that can be used to scan the codes and translations of the provided standard.
 * If invoked without parameters, the method will return an iterator to the standards
 * schemas.
 *
 * The {@link Standards()} method will return the list of standards, the {@link Types()}
 * method will return the list of data types, the {@link Languages()} method will return the
 * list of language codes and {@link Locales} will return the list of composite locales.
 *
 * We assume by default that English is the base language.
 *
 *	@package	Utils
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		25/08/2016
 *
 * @example
 * <code>
 * // Set directory paths.
 * $po = "/some/path/to/iso-codes";		// In general these are at the root level.
 * $json = "/some/path/to/iso-codes/data";	// In general these are in the data directory.
 *
 * // Instantiate class.
 * $iso = new ISOCodes( $json, $po );
 *
 * // Show list of standards.
 * print_r( $iso->Standards() );
 * // Show list of types.
 * print_r( $iso->Types() );
 * // Show list of locales.
 * print_r( $iso->Languages() );
 * // Show list of composite locales.
 * print_r( $iso->Locales() );
 *
 * // Iterate schemas.
 * $schemas = $iso->getIterator();
 * foreach( $schemas as $standard => $schema )
 * {
 * 	var_dump( $standard );
 * 	print_r( $schema );
 * }
 *
 * // Handle countries.
 * $countries = $iso->getIterator( ISOCodes::k3166_1 );
 * // Show title.
 * var_dump( $countries->Title() );
 * // Show description.
 * var_dump( $countries->Description() );
 * // Show key property.
 * var_dump( $countries->DefaultCode() );
 * // Show required properties.
 * print_r( $countries->Required() );
 * // Show translatable properties.
 * print_r( $countries->Translated() );
 * // Show properties.
 * print_r( $countries->Properties() );
 * // Show elements count.
 * var_dump( $countries->count() );
 * // Iterate element records.
 * foreach( $countries as $key => $data )
 * {
 * 	var_dump( $key );
 * 	print_r( $data );
 * }
 * </code>
 */
class ISOCodes
{
	/**
	 * <h4>Default language.</h4><p />
	 *
	 * This constant identifies the default language code, this implies that all elements
	 * must have at least this locale.
	 */
	const kDEFAULT_LANGUAGE = "en";

	/**
	 * <h4>ISO 639-2 base name.</h4><p />
	 *
	 * This constant identifies the ISO 639-2 standard (Codes for the representation of
	 * names of languages — Part 2: Alpha-3 code).
	 */
	const k639_2 = "639-2";
	
	/**
	 * <h4>ISO 639-3 base name.</h4><p />
	 *
	 * This constant identifies the ISO 639-3 standard (Codes for the representation of
	 * names of languages — Part 3: Alpha-3 code for comprehensive coverage of languages).
	 */
	const k639_3 = "639-3";
	
	/**
	 * <h4>ISO 639-5 base name.</h4><p />
	 *
	 * This constant identifies the ISO 639-5 standard (Codes for the representation of
	 * names of languages — Part 5: Alpha-3 code for language families and groups).
	 */
	const k639_5 = "639-5";
	
	/**
	 * <h4>ISO 3166-1 base name.</h4><p />
	 *
	 * This constant identifies the ISO 3166-1 standard (Codes for the representation of
	 * names of countries and their subdivisions – Part 1: Country codes).
	 */
	const k3166_1 = "3166-1";
	
	/**
	 * <h4>ISO 3166-2 base name.</h4><p />
	 *
	 * This constant identifies the ISO 3166-2 standard (Codes for the representation of
	 * names of countries and their subdivisions – Part 2: Country subdivision code).
	 */
	const k3166_2 = "3166-2";
	
	/**
	 * <h4>ISO 3166-3 base name.</h4><p />
	 *
	 * This constant identifies the ISO 3166-3 standard (Codes for the representation of
	 * names of countries and their subdivisions – Part 3: Code for formerly used names of
	 * countries).
	 */
	const k3166_3 = "3166-3";
	
	/**
	 * <h4>ISO 4217 base name.</h4><p />
	 *
	 * This constant identifies the ISO 4217 standard (International Standard for currency
	 * codes).
	 */
	const k4217 = "4217";

	/**
	 * <h4>ISO 15924 base name.</h4><p />
	 *
	 * This constant identifies the ISO 15924 standard (Codes for the representation of
	 * names of scripts).
	 */
	const k15924 = "15924";

	/**
	 * <h4>Source Json files directory.</h4><p />
	 *
	 * This data member holds the path to the source Json files.
	 *
	 * @var \SplFileInfo
	 */
	protected $mJson = NULL;
	
	/**
	 * <h4>Source <tt>.po</tt> files directory.</h4><p />
	 *
	 * This data member holds the path to the source <tt>.po</tt> files.
	 *
	 * @var \SplFileInfo
	 */
	protected $mPo = NULL;
	
	/**
	 * <h4>Destination files directory.</h4><p />
	 *
	 * This data member holds the path to the destination directory.
	 *
	 * @var \SplFileInfo
	 */
	protected $mDest = NULL;
	
	/**
	 * <h4>Standards codes.</h4><p />
	 *
	 * This data member holds the list of standards codes.
	 *
	 * @var array
	 */
	protected $mStandards = [];

	/**
	 * <h4>Language codes.</h4><p />
	 *
	 * This data member holds the list of language codes found in the <tt>.po</tt>
	 * directory.
	 *
	 * @var array
	 */
	protected $mLanguages = [];

	/**
	 * <h4>Schema.</h4><p />
	 *
	 * This data member holds the list of schemas.
	 *
	 * @var array
	 */
	protected $mSchema = [];

	/**
	 * <h4>Types.</h4><p />
	 *
	 * This data member collects the list of property types.
	 *
	 * @var array
	 */
	protected $mTypes = [];

	/**
	 * <h4>Locales.</h4><p />
	 *
	 * This data member holds the composite locales list.
	 *
	 * @var array
	 */
	protected $mLocales = [
		"aa_DJ" => "Afar (DJIBOUTI)",
		"aa_ER" => "Afar (ERITREA)",
		"aa_ER@saaho" => "Afar (ERITREA)",
		"aa_ET" => "Afar (ETHIOPIA)",
		"af_ZA" => "Afrikaans (SOUTH AFRICA)",
		"agr_PE" => "Aguaruna (PERU)",
		"ak_GH" => "Akan (GHANA)",
		"am_ET" => "Amharic (ETHIOPIA)",
		"an_ES" => "Aragonese (SPAIN)",
		"anp_IN" => "Angika (INDIA)",
		"ar_AE" => "Arabic (UNITED ARAB EMIRATES)",
		"ar_BH" => "Arabic (BAHRAIN)",
		"ar_DZ" => "Arabic (ALGERIA)",
		"ar_EG" => "Arabic (EGYPT)",
		"ar_IN" => "Arabic (INDIA)",
		"ar_IQ" => "Arabic (IRAQ)",
		"ar_JO" => "Arabic (JORDAN)",
		"ar_KW" => "Arabic (KUWAIT)",
		"ar_LB" => "Arabic (LEBANON)",
		"ar_LY" => "Arabic (LIBYAN ARAB JAMAHIRIYA)",
		"ar_MA" => "Arabic (MOROCCO)",
		"ar_OM" => "Arabic (OMAN)",
		"ar_QA" => "Arabic (QATAR)",
		"ar_SA" => "Arabic (SAUDI ARABIA)",
		"ar_SD" => "Arabic (SUDAN)",
		"ar_SS" => "Arabic (SOUTH SOUDAN)",
		"ar_SY" => "Arabic (SYRIAN ARAB REPUBLIC)",
		"ar_TN" => "Arabic (TUNISIA)",
		"ar_YE" => "Arabic (YEMEN)",
		"as_IN" => "Assamese (INDIA)",
		"ast_ES" => "Asturian (SPAIN)",
		"ayc_PE" => "Southern Aymara (PERU)",
		"ay_PE" => "Aymara (PERU)",
		"az_AZ" => "Azerbaijani (AZERBAIJAN)",
		"be_BY" => "Belarusian (BELARUS)",
		"be_BY@latin" => "Belarusian (BELARUS)",
		"bem_ZM" => "Bemba (Zambia) (ZAMBIA)",
		"ber_DZ" => "Berber languages (ALGERIA)",
		"ber_MA" => "Berber languages (MOROCCO)",
		"bg_BG" => "Bulgarian (BULGARIA)",
		"bhb_IN" => "Bhili (INDIA)",
		"bho_IN" => "Bhojpuri (INDIA)",
		"bi_TV" => "Bislama (TUVALU)",
		"bn_BD" => "Bengali (BANGLADESH)",
		"bn_IN" => "Bengali (INDIA)",
		"bo_CN" => "Tibetan (CHINA)",
		"bo_IN" => "Tibetan (INDIA)",
		"br_FR" => "Breton (FRANCE)",
		"br_FR@euro" => "Breton (FRANCE)",
		"brx_IN" => "Bodo (India) (INDIA)",
		"bs_BA" => "Bosnian (BOSNIA AND HERZEGOVINA)",
		"byn_ER" => "Bilin (ERITREA)",
		"ca_AD" => "Catalan (ANDORRA)",
		"ca_ES" => "Catalan (SPAIN)",
		"ca_ES@euro" => "Catalan (SPAIN)",
		"ca_FR" => "Catalan (FRANCE)",
		"ca_IT" => "Catalan (ITALY)",
		"ce_RU" => "Chechen (RUSSIAN FEDERATION)",
		"chr_US" => "Cherokee (UNITED STATES)",
		"cmn_TW" => "Mandarin Chinese (TAIWAN)",
		"crh_UA" => "Crimean Tatar (UKRAINE)",
		"csb_PL" => "Kashubian (POLAND)",
		"cs_CZ" => "Czech (CZECH REPUBLIC)",
		"cv_RU" => "Chuvash (RUSSIAN FEDERATION)",
		"cy_GB" => "Welsh (UNITED KINGDOM)",
		"da_DK" => "Danish (DENMARK)",
		"de_AT" => "German (AUSTRIA)",
		"de_AT@euro" => "German (AUSTRIA)",
		"de_BE" => "German (BELGIUM)",
		"de_BE@euro" => "German (BELGIUM)",
		"de_CH" => "German (SWITZERLAND)",
		"de_DE" => "German (GERMANY)",
		"de_DE@euro" => "German (GERMANY)",
		"de_IT" => "German (ITALY)",
		"de_LU" => "German (LUXEMBOURG)",
		"de_LU@euro" => "German (LUXEMBOURG)",
		"doi_IN" => "Dogri (macrolanguage) (INDIA)",
		"dv_MV" => "Dhivehi (MALDIVES)",
		"dz_BT" => "Dzongkha (BHUTAN)",
		"el_CY" => "Modern Greek (1453-) (CYPRUS)",
		"el_GR" => "Modern Greek (1453-) (GREECE)",
		"en_AG" => "English (ANTIGUA AND BARBUDA)",
		"en_AU" => "English (AUSTRALIA)",
		"en_BW" => "English (BOTSWANA)",
		"en_CA" => "English (CANADA)",
		"en_DK" => "English (DENMARK)",
		"en_GB" => "English (UNITED KINGDOM)",
		"en_HK" => "English (HONG KONG)",
		"en_IE" => "English (IRELAND)",
		"en_IE@euro" => "English (IRELAND)",
		"en_IL" => "English (ISRAEL)",
		"en_IN" => "English (INDIA)",
		"en_NG" => "English (NIGERIA)",
		"en_NZ" => "English (NEW ZEALAND)",
		"en_PH" => "English (PHILIPPINES)",
		"en_SG" => "English (SINGAPORE)",
		"en_US" => "English (UNITED STATES)",
		"en_ZA" => "English (SOUTH AFRICA)",
		"en_ZM" => "English (ZAMBIA)",
		"en_ZW" => "English (ZIMBABWE)",
		"es_AR" => "Spanish (ARGENTINA)",
		"es_BO" => "Spanish (BOLIVIA, PLURINATIONAL STATE OF)",
		"es_CL" => "Spanish (CHILE)",
		"es_CO" => "Spanish (COLOMBIA)",
		"es_CR" => "Spanish (COSTA RICA)",
		"es_CU" => "Spanish (CUBA)",
		"es_DO" => "Spanish (DOMINICAN REPUBLIC)",
		"es_EC" => "Spanish (ECUADOR)",
		"es_ES" => "Spanish (SPAIN)",
		"es_ES@euro" => "Spanish (SPAIN)",
		"es_GT" => "Spanish (GUATEMALA)",
		"es_HN" => "Spanish (HONDURAS)",
		"es_MX" => "Spanish (MEXICO)",
		"es_NI" => "Spanish (NICARAGUA)",
		"es_PA" => "Spanish (PANAMA)",
		"es_PE" => "Spanish (PERU)",
		"es_PR" => "Spanish (PUERTO RICO)",
		"es_PY" => "Spanish (PARAGUAY)",
		"es_SV" => "Spanish (EL SALVADOR)",
		"es_US" => "Spanish (UNITED STATES)",
		"es_UY" => "Spanish (URUGUAY)",
		"es_VE" => "Spanish (VENEZUELA, BOLIVARIAN REPUBLIC OF)",
		"et_EE" => "Estonian (ESTONIA)",
		"eu_ES" => "Basque (SPAIN)",
		"eu_ES@euro" => "Basque (SPAIN)",
		"fa_IR" => "Persian (IRAN, ISLAMIC REPUBLIC OF)",
		"ff_SN" => "Fulah (SENEGAL)",
		"fi_FI" => "Finnish (FINLAND)",
		"fi_FI@euro" => "Finnish (FINLAND)",
		"fil_PH" => "Filipino (PHILIPPINES)",
		"fo_FO" => "Faroese (FAROE ISLANDS)",
		"fr_BE" => "French (BELGIUM)",
		"fr_BE@euro" => "French (BELGIUM)",
		"fr_CA" => "French (CANADA)",
		"fr_CH" => "French (SWITZERLAND)",
		"fr_FR" => "French (FRANCE)",
		"fr_FR@euro" => "French (FRANCE)",
		"fr_LU" => "French (LUXEMBOURG)",
		"fr_LU@euro" => "French (LUXEMBOURG)",
		"fur_IT" => "Friulian (ITALY)",
		"fy_DE" => "Western Frisian (GERMANY)",
		"fy_NL" => "Western Frisian (NETHERLANDS)",
		"ga_IE" => "Irish (IRELAND)",
		"ga_IE@euro" => "Irish (IRELAND)",
		"gd_GB" => "Scottish Gaelic (UNITED KINGDOM)",
		"gez_ER" => "Geez (ERITREA)",
		"gez_ER@abegede" => "Geez (ERITREA)",
		"gez_ET" => "Geez (ETHIOPIA)",
		"gez_ET@abegede" => "Geez (ETHIOPIA)",
		"gl_ES" => "Galician (SPAIN)",
		"gl_ES@euro" => "Galician (SPAIN)",
		"gn_PY@Latin" => "Guarani (PARAGUAY)",
		"gu_IN" => "Gujarati (INDIA)",
		"gv_GB" => "Manx (UNITED KINGDOM)",
		"hak_TW" => "Hakka Chinese (TAIWAN)",
		"ha_NG" => "Hausa (NIGERIA)",
		"he_IL" => "Hebrew (ISRAEL)",
		"hi_IN" => "Hindi (INDIA)",
		"hne_IN" => "Chhattisgarhi (INDIA)",
		"hr_HR" => "Croatian (CROATIA)",
		"hsb_DE" => "Upper Sorbian (GERMANY)",
		"ht_HT" => "Haitian (HAITI)",
		"hu_HU" => "Hungarian (HUNGARY)",
		"hus_MX" => "Huastec (MEXICO)",
		"hy_AM" => "Armenian (ARMENIA)",
		"ia_FR" => "Interlingua (International Auxiliary Language Association) (FRANCE)",
		"id_ID" => "Indonesian (INDONESIA)",
		"ig_NG" => "Igbo (NIGERIA)",
		"ik_CA" => "Inupiaq (CANADA)",
		"is_IS" => "Icelandic (ICELAND)",
		"it_CH" => "Italian (SWITZERLAND)",
		"it_IT" => "Italian (ITALY)",
		"it_IT@euro" => "Italian (ITALY)",
		"iu_CA" => "Inuktitut (CANADA)",
		"ja_JP" => "Japanese (JAPAN)",
		"kab_DZ" => "Kabyle (ALGERIA)",
		"ka_GE" => "Georgian (GEORGIA)",
		"kk_KZ" => "Kazakh (KAZAKHSTAN)",
		"kl_GL" => "Kalaallisut (GREENLAND)",
		"km_KH" => "Central Khmer (CAMBODIA)",
		"kn_IN" => "Kannada (INDIA)",
		"kok_IN" => "Konkani (macrolanguage) (INDIA)",
		"ko_KR" => "Korean (KOREA, REPUBLIC OF)",
		"ks_IN" => "Kashmiri (INDIA)",
		"ks_IN@devanagari" => "Kashmiri (INDIA)",
		"ku_TR" => "Kurdish (TURKEY)",
		"kw_GB" => "Cornish (UNITED KINGDOM)",
		"ky_KG" => "Kirghiz (KYRGYZSTAN)",
		"lb_LU" => "Luxembourgish (LUXEMBOURG)",
		"lg_UG" => "Ganda (UGANDA)",
		"li_BE" => "Limburgan (BELGIUM)",
		"lij_IT" => "Ligurian (ITALY)",
		"li_NL" => "Limburgan (NETHERLANDS)",
		"ln_CD" => "Lingala (CONGO, THE DEMOCRATIC REPUBLIC OF THE)",
		"lo_LA" => "Lao (LAO PEOPLE'S DEMOCRATIC REPUBLIC)",
		"lt_LT" => "Lithuanian (LITHUANIA)",
		"lv_LV" => "Latvian (LATVIA)",
		"lzh_TW" => "Literary Chinese (TAIWAN)",
		"mag_IN" => "Magahi (INDIA)",
		"mai_IN" => "Maithili (INDIA)",
		"mg_MG" => "Malagasy (MADAGASCAR)",
		"mh_MH" => "Marshallese (MARSHALL ISLANDS)",
		"mhr_RU" => "Eastern Mari (RUSSIAN FEDERATION)",
		"mi_NZ" => "Maori (NEW ZEALAND)",
		"miq_NI" => "Mískito (NICARAGUA)",
		"mk_MK" => "Macedonian (MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF)",
		"ml_IN" => "Malayalam (INDIA)",
		"mni_IN" => "Manipuri (INDIA)",
		"mn_MN" => "Mongolian (MONGOLIA)",
		"mr_IN" => "Marathi (INDIA)",
		"ms_MY" => "Malay (macrolanguage) (MALAYSIA)",
		"mt_MT" => "Maltese (MALTA)",
		"my_MM" => "Burmese (MYANMAR)",
		"myv_RU" => "Erzya (RUSSIAN FEDERATION)",
		"myv_RU@cyrillic" => "Erzya (RUSSIAN FEDERATION)",
		"nah_MX" => "Nahuatl languages (MEXICO)",
		"nan_TW" => "Min Nan Chinese (TAIWAN)",
		"nan_TW@latin" => "Min Nan Chinese (TAIWAN)",
		"nb_NO" => "Norwegian Bokmål (NORWAY)",
		"nds_DE" => "Low German (GERMANY)",
		"nds_NL" => "Low German (NETHERLANDS)",
		"ne_NP" => "Nepali (NEPAL)",
		"nhn_MX" => "Central Nahuatl (MEXICO)",
		"niu_NU" => "Niuean (NIUE)",
		"niu_NZ" => "Niuean (NEW ZEALAND)",
		"nl_AW" => "Dutch (ARUBA)",
		"nl_BE" => "Dutch (BELGIUM)",
		"nl_BE@euro" => "Dutch (BELGIUM)",
		"nl_NL" => "Dutch (NETHERLANDS)",
		"nl_NL@euro" => "Dutch (NETHERLANDS)",
		"nn_NO" => "Norwegian Nynorsk (NORWAY)",
		"nr_ZA" => "South Ndebele (SOUTH AFRICA)",
		"nso_ZA" => "Pedi (SOUTH AFRICA)",
		"oc_FR" => "Occitan (post 1500) (FRANCE)",
		"om_ET" => "Oromo (ETHIOPIA)",
		"om_KE" => "Oromo (KENYA)",
		"or_IN" => "Oriya (INDIA)",
		"os_RU" => "Ossetian (RUSSIAN FEDERATION)",
		"pa_IN" => "Panjabi (INDIA)",
		"pap_AN" => "Papiamento (NETHERLANDS ANTILLES)",
		"pap_AW" => "Papiamento (ARUBA)",
		"pap_CW" => "Papiamento (CURAÇAO)",
		"pa_PK" => "Panjabi (PAKISTAN)",
		"pl_PL" => "Polish (POLAND)",
		"ps_AF" => "Pushto (AFGHANISTAN)",
		"pt_BR" => "Portuguese (BRAZIL)",
		"pt_PT" => "Portuguese (PORTUGAL)",
		"pt_PT@euro" => "Portuguese (PORTUGAL)",
		"quy_PE" => "Ayacucho Quechua (PERU)",
		"quz_PE" => "Cusco Quechua (PERU)",
		"raj_IN" => "Rajasthani (INDIA)",
		"ro_RO" => "Romanian (ROMANIA)",
		"ru_RU" => "Russian (RUSSIAN FEDERATION)",
		"ru_UA" => "Russian (UKRAINE)",
		"rw_RW" => "Kinyarwanda (RWANDA)",
		"sa_IN" => "Sanskrit (INDIA)",
		"sat_IN" => "Santali (INDIA)",
		"sc_IT" => "Sardinian (ITALY)",
		"sd_IN" => "Sindhi (INDIA)",
		"sd_IN@devanagari" => "Sindhi (INDIA)",
		"se_NO" => "Northern Sami (NORWAY)",
		"sgs_LT" => "Samogitian (LITHUANIA)",
		"shs_CA" => "Shuswap (CANADA)",
		"sid_ET" => "Sidamo (ETHIOPIA)",
		"si_LK" => "Sinhala (SRI LANKA)",
		"sk_SK" => "Slovak (SLOVAKIA)",
		"sl_SI" => "Slovenian (SLOVENIA)",
		"so_DJ" => "Somali (DJIBOUTI)",
		"so_ET" => "Somali (ETHIOPIA)",
		"so_KE" => "Somali (KENYA)",
		"son_ML" => "Songhai languages (MALI)",
		"so_SO" => "Somali (SOMALIA)",
		"sq_AL" => "Albanian (ALBANIA)",
		"sq_KV" => "Albanian (KOSOVO)",
		"sq_MK" => "Albanian (MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF)",
		"sr_ME" => "Serbian (MONTENEGRO)",
		"sr_RS" => "Serbian (SERBIA)",
		"sr_RS@latin" => "Serbian (SERBIA)",
		"ss_ZA" => "Swati (SOUTH AFRICA)",
		"st_ZA" => "Southern Sotho (SOUTH AFRICA)",
		"sv_FI" => "Swedish (FINLAND)",
		"sv_FI@euro" => "Swedish (FINLAND)",
		"sv_SE" => "Swedish (SWEDEN)",
		"sw_KE" => "Swahili (macrolanguage) (KENYA)",
		"sw_TZ" => "Swahili (macrolanguage) (TANZANIA, UNITED REPUBLIC OF)",
		"szl_PL" => "Silesian (POLAND)",
		"ta_IN" => "Tamil (INDIA)",
		"ta_LK" => "Tamil (SRI LANKA)",
		"tcy_IN" => "Tulu (INDIA)",
		"te_IN" => "Telugu (INDIA)",
		"tg_TJ" => "Tajik (TAJIKISTAN)",
		"the_NP" => "Chitwania Tharu (NEPAL)",
		"th_TH" => "Thai (THAILAND)",
		"ti_ER" => "Tigrinya (ERITREA)",
		"ti_ET" => "Tigrinya (ETHIOPIA)",
		"tig_ER" => "Tigre (ERITREA)",
		"tk_TM" => "Turkmen (TURKMENISTAN)",
		"tl_PH" => "Tagalog (PHILIPPINES)",
		"tn_ZA" => "Tswana (SOUTH AFRICA)",
		"tr_CY" => "Turkish (CYPRUS)",
		"tr_TR" => "Turkish (TURKEY)",
		"ts_ZA" => "Tsonga (SOUTH AFRICA)",
		"tt_RU" => "Tatar (RUSSIAN FEDERATION)",
		"tt_RU@iqtelif" => "Tatar (RUSSIAN FEDERATION)",
		"ug_CN" => "Uighur (CHINA)",
		"uk_UA" => "Ukrainian (UKRAINE)",
		"unm_US" => "Unami (UNITED STATES)",
		"ur_IN" => "Urdu (INDIA)",
		"ur_PK" => "Urdu (PAKISTAN)",
		"uz_UZ" => "Uzbek (UZBEKISTAN)",
		"uz_UZ@cyrillic" => "Uzbek (UZBEKISTAN)",
		"ve_ZA" => "Venda (SOUTH AFRICA)",
		"vi_VN" => "Vietnamese (VIET NAM)",
		"wa_BE" => "Walloon (BELGIUM)",
		"wa_BE@euro" => "Walloon (BELGIUM)",
		"wae_CH" => "Walser (SWITZERLAND)",
		"wal_ET" => "Wolaytta (ETHIOPIA)",
		"wo_SN" => "Wolof (SENEGAL)",
		"xh_ZA" => "Xhosa (SOUTH AFRICA)",
		"yi_US" => "Yiddish (UNITED STATES)",
		"yo_NG" => "Yoruba (NIGERIA)",
		"yue_HK" => "Yue Chinese (HONG KONG)",
		"zh_CN" => "Chinese (CHINA)",
		"zh_HK" => "Chinese (HONG KONG)",
		"zh_SG" => "Chinese (SINGAPORE)",
		"zh_TW" => "Chinese (TAIWAN)",
		"zu_ZA" => "Zulu (SOUTH AFRICA)"
	];




/*=======================================================================================
 *																						*
 *										MAGIC											*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate class.</h4><p />
	 *
	 * The method will check the provided directory parameters and compile the list of
	 * languages.
	 *
	 * @param string			$theJson    		Json files path.
	 * @param string			$thePo      		PO files path.
	 * @throws \InvalidArgumentException
	 *
	 * @uses loadStandards()
	 * @uses loadLanguages()
	 * @uses loadSchema()
	 *
	 * @example
	 * <code>
	 * // Set directory paths.
	 * $po = "/some/path/to/iso-codes";
	 * $json = "/some/path/to/iso-codes/data";	// In general that is where they are.
	 *
	 * // Instantiate class.
	 * $iso = new ISOCodes( $json, $po );
	 * </code>
	 */
	public function __construct( string $theJson, string $thePo )
	{
		//
		// Check source directories.
		//
		$this->mJson = new \SplFileInfo( $theJson );
		if( ! $this->mJson->isDir() )
			throw new \InvalidArgumentException(
				"Json files source parameter is not a directory."
			);                                                                  // !@! ==>
		if( ! $this->mJson->isReadable() )
			throw new \InvalidArgumentException(
				"Json files source parameter is not readable."
			);                                                                  // !@! ==>
		$this->mPo = new \SplFileInfo( $thePo );
		if( ! $this->mPo->isDir() )
			throw new \InvalidArgumentException(
				"PO files source parameter is not a directory."
			);                                                                  // !@! ==>
		if( ! $this->mPo->isReadable() )
			throw new \InvalidArgumentException(
				"PO files source parameter is not readable."
			);                                                                  // !@! ==>
		
		//
		// Load standards.
		//
		$this->mStandards = $this->loadStandards();

		//
		// Load languages.
		//
		$this->mLanguages = $this->loadLanguages();

		//
		// Load schema.
		//
		$this->mSchema = $this->loadSchema();

	} // Constructor.



/*=======================================================================================
 *																						*
 *							PUBLIC MEMBER ACCESSOR INTERFACE	    					*
 *																						*
 *======================================================================================*/
	
	
	
	/*===================================================================================
	 *	Standards																		*
	 *==================================================================================*/
	
	/**
	 * <h4>Return standards list.</h4><p />
	 *
	 * This method can be used to retrieve the list of standards.
	 *
	 * @return array				List of standards codes.
	 */
	public function Standards()
	{
		return $this->mStandards;													// ==>
		
	} // Standards.


	/*===================================================================================
	 *	Languages																		*
	 *==================================================================================*/

	/**
	 * <h4>Return languages list.</h4><p />
	 *
	 * This method can be used to retrieve the list of language codes.
	 *
	 * @return array				List of language codes.
	 */
	public function Languages()
	{
		return $this->mLanguages;													// ==>

	} // Languages.


	/*===================================================================================
	 *	Locales																			*
	 *==================================================================================*/

	/**
	 * <h4>Return locales.</h4><p />
	 *
	 * This method can be used to retrieve the composite locales, that is, the locales which
	 * are not strictly language codes.
	 *
	 * The resulting array has as key the locale code and as value the name in
	 * <em>English</em>.
	 *
	 * @return array				List of locales.
	 */
	public function Locales()
	{
		return $this->mLocales;														// ==>

	} // Locales.


	/*===================================================================================
	 *	Types																			*
	 *==================================================================================*/

	/**
	 * <h4>Return schema types.</h4><p />
	 *
	 * This method can be used to retrieve the schema types.
	 *
	 * @return array				List of schema types.
	 */
	public function Types()
	{
		return $this->mTypes;														// ==>

	} // Types.



/*=======================================================================================
 *																						*
 *								PUBLIC OPERATIONS INTERFACE	    						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	getIterator																		*
	 *==================================================================================*/

	/**
	 * <h4>Return a standards iterator.</h4><p />
	 *
	 * This method can be used to retrieve an iterator that can be used to scan the codes
	 * and translations of the provided standard.
	 *
	 * If the provided standard is not supported, the method will raise an exception.
	 *
	 * If the parameter is omitted, the method will return an iterator to the standards
	 * schemas.
	 *
	 * @param string				$theStandard	 	Requested standard.
	 * @return \Iterator			Standards or schemas iterator.
	 * @throws \InvalidArgumentException
	 *
	 * @example
	 * <code>
	 * // Set directory paths.
	 * $po = "/some/path/to/iso-codes";
	 * $json = "/some/path/to/iso-codes/data";	// In general that is where they are.
	 *
	 * // Instantiate class.
	 * $iso = new ISOCodes( $json, $po );
	 *
	 * // Show standards.
	 * print_r( $iso->Standards() );
	 * // Show types.
	 * print_r( $iso->Types() );
	 * // Show locales.
	 * print_r( $iso->Languages() );
	 *
	 * // Iterate schemas.
	 * $schemas = $iso->getIterator();
	 * foreach( $schemas as $standard => $schema )
	 * {
	 *
	 * }
	 * 	...
	 *
	 * // Iterate countries.
	 * $countries = $iso->getIterator( ISOCodes::k3166_1 );
	 * foreach( $countries as $code => $data )
	 * 	...
	 * </code>
	 */
	public function getIterator( string $theStandard = NULL )
	{
		//
		// Handle schema iterator.
		//
		if( $theStandard === NULL )
			return new \ArrayIterator( $this->mSchema );							// ==>

		//
		// Parse by standard.
		//
		switch( $theStandard )
		{
			//
			// Supported standards.
			//
			case self::k639_2:
			case self::k639_3:
			case self::k639_5:
			case self::k3166_1:
			case self::k3166_2:
			case self::k3166_3:
			case self::k4217:
			case self::k15924:
				$name
					= "Milko\\utils\\" .
					  "ISO_" .
					  str_replace( '-', '_', $theStandard ) .
					  "_Iterator";
				return
					new $name(
						json_decode(
							file_get_contents(
								$this->mJson->getRealPath() .
								DIRECTORY_SEPARATOR .
								"iso_" .
								$theStandard .
								".json"
							),
							TRUE
						)[ $theStandard ],
						new \SplFileInfo(
							$this->mPo->getRealPath() .
							DIRECTORY_SEPARATOR .
							"iso_" .
							$theStandard
						),
						$this->mSchema[ $theStandard ]
					);																// ==>

		} // Parsing standard.

		throw new \InvalidArgumentException(
			"Unsupported standard [ISO-$theStandard]."
		);																		// !@! ==>

	} // getIterator.



/*=======================================================================================
 *																						*
 *								PROTECTED LOADING INTERFACE								*
 *																						*
 *======================================================================================*/
	
	
	
	/*===================================================================================
	 *	loadStandards																	*
	 *==================================================================================*/
	
	/**
	 * <h4>Load standards codes.</h4><p />
	 *
	 * This method will load the standards in a data member for use by methods.
	 *
	 * @return array				List of standards.
	 */
	protected function loadStandards()
	{
		return [
			self::k639_2, self::k639_3, self::k639_5,
			self::k3166_1, self::k3166_2, self::k3166_3,
			self::k4217, self::k15924
		];																			// ==>
		
	} // loadStandards.


	/*===================================================================================
	 *	loadLanguages																	*
	 *==================================================================================*/

	/**
	 * <h4>Load language codes.</h4><p />
	 *
	 * This method will parse the PO files directory and return the prefix of all
	 * <tt>.po</tt> file names, which correspond to the language codes.
	 *
	 * @return array				List of languages.
	 */
	protected function loadLanguages()
	{
		//
		// Init local storage.
		//
		$langs = [];

		//
		// Iterate PO directory directory.
		//
		foreach( new \DirectoryIterator( $this->mPo ) as $file )
		{
			//
			// Skip dots.
			//
			if( $file->isDot() )
				continue;														// =>

			//
			// Handle PO directory.
			//
			if( $file->isDir()
			 && (substr( $file->getBasename(), 0, 4 ) == "iso_")
			 && in_array( substr( $file->getBasename(), 4 ), $this->mStandards ) )
			{
				//
				// Iterate directory.
				//
				foreach( new \DirectoryIterator( $file->getRealPath() ) as $sub )
				{
					//
					// Handle PO file.
					//
					if( $sub->isFile()
					 && (strtolower( $ext = $sub->getExtension() ) == "po") )
						$langs[] = $sub->getBasename( ".$ext" );

				} // Iterating PO files directory.

			} // Possible PO files directory.

		} // Iterating PO directory directory.

		//
		// Normalise list.
		//
		$langs = array_unique( $langs );
		asort( $langs );

		return array_values( $langs );												// ==>

	} // loadLanguages.


	/*===================================================================================
	 *	loadSchema																		*
	 *==================================================================================*/

	/**
	 * <h4>Load schema.</h4><p />
	 *
	 * This method will parse the schema files and load them into the object's member.
	 *
	 * @return array				List of schemas.
	 */
	protected function loadSchema()
	{
		//
		// Init local storage.
		//
		$schemas = [];

		//
		// Iterate Json files directory.
		//
		foreach( new \DirectoryIterator( $this->mJson ) as $file )
		{
			//
			// Skip dots.
			//
			if( $file->isDot() )
				continue;														// =>

			//
			// Select Json files.
			//
			if( $file->isFile()
			 && (strtolower( $ext = $file->getExtension() ) == "json") )
			{
				//
				// Select schema files.
				//
				if( (substr( $file->getBasename( ".$ext" ), 0, 7 ) == "schema-")
				 && in_array( substr( $file->getBasename( ".$ext" ), 7 ),
							  $this->mStandards ) )
				{
					//
					// Load schema.
					//
					$schema
						= json_decode(
							file_get_contents( $file->getRealPath() ),
							TRUE
					);

					//
					// Get schema code.
					// We assume the "properties" element is not empty,
					// we get the first and only element.
					//
					foreach( $schema[ "properties" ] as $code => $value ) break;

					//
					// Load schema attributes.
					//
					$schemas[ $code ] = [];
					if( array_key_exists( '$schema', $schema ) )
						$schemas[ $code ][ '$schema' ] = $schema[ '$schema' ];
					if( array_key_exists( "title", $schema ) )
						$schemas[ $code ][ "title" ] = $schema[ "title" ];
					if( array_key_exists( 'description', $schema ) )
						$schemas[ $code ][ "description" ] = $schema[ "description" ];
					if( array_key_exists( "required",
										  $schema[ "properties" ][ $code ][ "items" ] ) )
						$schemas[ $code ][ "required" ]
							= $schema[ "properties" ][ $code ][ "items" ][ "required" ];

					//
					// Load properties.
					//
					$schemas[ $code ][ "properties" ]
						= $schema[ "properties" ][ $code ][ "items" ][ "properties" ];

					//
					// Collect types.
					//
					foreach( $schemas[ $code ][ "properties" ] as $prop )
						$this->mTypes[] = $prop[ "type" ];

				} // Is schema file.

			} // Json file.

		} // Iterating Json files directory.

		//
		// Normalise types.
		//
		$this->mTypes = array_values( array_unique( $this->mTypes ) );

		return $schemas;															// ==>

	} // loadSchema.




} // class ISOCodes.


?>
