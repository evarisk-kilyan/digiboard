<?php
/* Copyright (C) 2024 EVARISK <technique@evarisk.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    class/digiboarddashboard.class.php
 * \ingroup digiboard
 * \brief   Class file for manage DigiBoardDashboard
 */

/**
 * Class for DigiBoardDashboard
 */
class DigiboardDashboard
{
    /**
     * @var DoliDB Database handler
     */
    public DoliDB $db;

    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct(DoliDB $db)
    {
        $this->db = $db;
    }

    /**
     * Load dashboard info
     *
     * @return array
     * @throws Exception
     */
    public function load_dashboard(): array
    {
        $array['lists'] = [];

        if (isModEnabled('digiriskdolibarr') && isModEnabled('multicompany')) {
            $getDigiRiskStatsList = $this->getDigiRiskStatsList();
            $array['digiriskdolibarr']['lists'] = [$getDigiRiskStatsList];
        }

        return $array;
    }

    /**
     * Get digirisk stats list with API
     *
     * @return array     Graph datas (label/color/type/title/data etc..)
     * @throws Exception
     */
    public function getDigiRiskStatsList(): array
    {
        global $db, $mc, $langs;

        // Graph Title parameters
        $array['title'] = $langs->transnoentities('DigiRiskStatsList');
        $array['picto'] = 'digiriskdolibarr_color@digiriskdolibarr';

        // Graph parameters
        $array['type']   = 'list';
        $array['labels'] = ['Site', 'Siret', 'RiskAssessmentDocument', 'DelayGenerateDate', 'NbEmployees', 'NbEmployeesInvolved', 'GreyRisk', 'OrangeRisk', 'RedRisk', 'BlackRisk', 'NbPresquAccidents', 'NbAccidents', 'NbAccidentsByEmployees', 'NbAccidentInvestigations', 'WorkStopDays', 'FrequencyIndex', 'FrequencyRate', 'GravityRate'];

        require_once __DIR__ . '/../../digiriskdolibarr/class/digiriskdolibarrdocuments/riskassessmentdocument.class.php';
        require_once __DIR__ . '/../../digiriskdolibarr/class/evaluator.class.php';
        require_once __DIR__ . '/../../digiriskdolibarr/class/digiriskelement.class.php';
        require_once __DIR__ . '/../../digiriskdolibarr/class/riskanalysis/risk.class.php';
        require_once __DIR__ . '/../../digiriskdolibarr/class/accident.class.php';

        $riskAssessmentDocument = new RiskAssessmentDocument($this->db);
        $evaluator              = new Evaluator($this->db);
        $digiriskElement        = new DigiriskElement($this->db);
        $risk                   = new Risk($this->db);
        $accident               = new Accident($this->db);

        $riskAssessmentDocument->ismultientitymanaged = 0;
        $accident->ismultientitymanaged               = 0;

        $arrayDigiRiskStatsList = [];
        $entities               = [];
        $filter                 = '';
        $riskAssessmentCotation = [1 => 'GreyRisk', 2 => 'OrangeRisk', 3 => 'RedRisk', 4 => 'BlackRisk'];
        $sharingEntities        = $mc->sharings['digiriskstats'];
        if (!empty($sharingEntities)) {
            $currentEntity[]           = 1;
            $sharingEntitiesAndCurrent = array_unique(array_merge($currentEntity, $sharingEntities));
            $entities                  = $mc->getEntitiesList(false, false, true);
            $filter                    = $digiriskElement->getTrashExclusionSqlFilter();
        }
        if (!empty($sharingEntitiesAndCurrent)) {
            foreach ($sharingEntitiesAndCurrent as $key => $sharingEntity) {
                $arrayDigiRiskStatsList[$key]['Site']['value']   = $entities[$sharingEntity];
                $arrayDigiRiskStatsList[$key]['Site']['morecss'] = 'left bold';
                $arrayDigiRiskStatsList[$key]['Siret']['value']  = dolibarr_get_const($db, 'MAIN_INFO_SIRET', $sharingEntity);

                $moreParam['entity']                                             = $sharingEntity;
                $filterEntity                                                    = ' AND t.entity = ' . $sharingEntity;
                $moreParam['filter']                                             = $filterEntity;
                $arrayGetGenerationDateInfos                                     = $riskAssessmentDocument->getGenerationDateInfos($moreParam);
                $arrayDigiRiskStatsList[$key]['RiskAssessmentDocument']['value'] = $arrayGetGenerationDateInfos['lastgeneratedate'] . $arrayGetGenerationDateInfos['moreContent'];
                $arrayDigiRiskStatsList[$key]['DelayGenerateDate']['value']      = $arrayGetGenerationDateInfos['delaygeneratedate'];

                $moreParam['filter']                                          = ' AND u.entity IN (0,' . $sharingEntity . ')';
                $employees                                                    = $evaluator->getNbEmployees($moreParam);
                $arrayDigiRiskStatsList[$key]['NbEmployees']['value']         = $employees['nbemployees'];
                $arrayDigiRiskStatsList[$key]['NbEmployeesInvolved']['value'] = $evaluator->getNbEmployeesInvolved($moreParam)['nbemployeesinvolved'];

                $moreParam['filter']                = $filter . $filterEntity;
                $moreParam['multiEntityManagement'] = false;
                $getRisksByCotation = $risk->getRisksByCotation($moreParam)['data'];
                for ($i = 1; $i <= 4; $i++) {
                    $arrayDigiRiskStatsList[$key][$riskAssessmentCotation[$i]]['value']    = $getRisksByCotation[$i];
                    $arrayDigiRiskStatsList[$key][$riskAssessmentCotation[$i]]['morecss']  = 'risk-evaluation-cotation';
                    $arrayDigiRiskStatsList[$key][$riskAssessmentCotation[$i]]['moreAttr'] = 'data-scale=' . $i . ' style="line-height: 0; border-radius: 0;"';
                }

                $moreParam['filter']    = $filterEntity;
                $join                   = ' LEFT JOIN ' . MAIN_DB_PREFIX . $accident->table_element . ' as a ON a.rowid = t.fk_accident';
                $accidentsWithWorkStops = saturne_fetch_all_object_type('AccidentWorkStop', 'DESC', 't.rowid', 0, 0, ['customsql' => 't.entity = ' . $sharingEntity], 'AND', false, false, false, $join);
                $accidents              = $accident->fetchAll('', '', 0, 0, ['customsql' => ' t.status > ' . Accident::STATUS_DRAFT . $moreParam['filter']]);
                if (empty($accidents) && !is_array($accidents)) {
                    $accidents = [];
                }
                if (empty($accidentsWithWorkStops) && !is_array($accidentsWithWorkStops)) {
                    $accidentsWithWorkStops = [];
                }

                $arrayDigiRiskStatsList[$key]['NbPresquAccidents']['value']        = $accident->getNbPresquAccidents()['nbpresquaccidents'];
                $arrayDigiRiskStatsList[$key]['NbAccidents']['value']              = $accident->getNbAccidents($accidents, $accidentsWithWorkStops)['data']['accidents'];
                $arrayDigiRiskStatsList[$key]['NbAccidentsByEmployees']['value']   = $accident->getNbAccidentsByEmployees($accidents, $accidentsWithWorkStops, $employees)['nbaccidentsbyemployees'];
                $arrayDigiRiskStatsList[$key]['NbAccidentInvestigations']['value'] = $accident->getNbAccidentInvestigations($moreParam)['nbaccidentinvestigations'];
                $arrayDigiRiskStatsList[$key]['WorkStopDays']['value']             = $accident->getNbWorkstopDays($accidentsWithWorkStops)['nbworkstopdays'];
                $arrayDigiRiskStatsList[$key]['FrequencyIndex']['value']           = $accident->getFrequencyIndex($accidentsWithWorkStops, $employees)['frequencyindex'];
                $arrayDigiRiskStatsList[$key]['FrequencyRate']['value']            = $accident->getFrequencyRate($employees)['frequencyrate'];
                $arrayDigiRiskStatsList[$key]['GravityRate']['value']              = $accident->getGravityRate($employees)['gravityrate'];
            }
        }
        $array['data'] = $arrayDigiRiskStatsList;

        return $array;
    }
}
