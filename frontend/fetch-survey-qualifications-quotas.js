import http from "@/api";

export class SurveyQualificationsQuotaService {

  static async fetchSurveyOffers(platformId, pageNum = 1, pageSize = 20, sortBy = 'update_time', sortOrder = 'desc') {
    const requestParams = {
      page: pageNum,
      limit: pageSize,
      platform_id: platformId,
      search_field: 'project_name',
      search_exp: 'like',
      date_field: 'create_time',
      sort_field: sortBy,
      sort_value: sortOrder
    };

    try {
      const response = await http.get(`/api/member.Platform/offers`, requestParams, { loading: false });
      return {
        success: true,
        data: response.data,
        surveyList: response.data.list,
        totalCount: response.data.count,
        visibilityFlags: {
          showQuota: response.data.show_quota,
          showName: response.data.show_name,
          showLoi: response.data.show_loi,
          showIr: response.data.show_ir,
          showComplete: response.data.show_complete,
          showClick: response.data.show_click
        },
        rawResponse: response
      };
    } catch (error) {
      console.error('Error fetching survey offers:', error);
      return {
        success: false,
        error: error.message,
        surveyList: [],
        totalCount: 0
      };
    }
  }

  static async fetchQuotaDetails(projectNumber) {
    const requestParams = {
      project_pno: projectNumber
    };

    try {
      const response = await http.get(`/api/member.Platform/quota`, requestParams, { loading: false });
      const quotaData = response.data;

      return {
        success: true,
        quotaType: quotaData.type,
        quotaContent: quotaData.content,
        isHtmlType: quotaData.type === 'html',
        isLinkType: quotaData.type === 'link',
        displayFormat: quotaData.type === 'html' ? 'HTML Markup' : 'External URL',
        rawResponse: response
      };
    } catch (error) {
      console.error('Error fetching quota details:', error);
      return {
        success: false,
        error: error.message,
        quotaType: null,
        quotaContent: null
      };
    }
  }

  static parseSurveyQualifications(surveyItem) {
    if (!surveyItem) {
      return {
        error: 'Survey item is null or undefined'
      };
    }

    const qualifications = {
      quotaStatus: {
        total: surveyItem.project_quota,
        completed: surveyItem.project_complete,
        remaining: surveyItem.project_quota - surveyItem.project_complete,
        percentageFilled: ((surveyItem.project_complete / surveyItem.project_quota) * 100).toFixed(2),
        isFull: surveyItem.project_complete >= surveyItem.project_quota
      },
      surveyTitle: surveyItem.project_name,
      projectNumber: surveyItem.project_pno,
      countryCode: surveyItem.code,
      durationMinutes: surveyItem.project_loi,
      incidenceRate: surveyItem.project_ir,
      rewardAmount: {
        rawValue: surveyItem.project_cpi,
        inCents: surveyItem.project_cpi,
        inUSD: (surveyItem.project_cpi / 100).toFixed(2),
        displayFormat: `$${(surveyItem.project_cpi / 100).toFixed(2)}`
      },
      customQualifications: SurveyQualificationsQuotaService.parseCustomParameters(surveyItem.project_params),
      platformInfo: {
        id: surveyItem.platform.platform_id,
        name: surveyItem.platform.platform_name,
        hasDetailedQuota: surveyItem.platform.is_quota === 1
      },
      timestamps: {
        createdAt: surveyItem.create_time,
        updatedAt: surveyItem.update_time
      }
    };

    return qualifications;
  }

  static parseCustomParameters(projectParamsString) {
    if (!projectParamsString) {
      return {
        parsed: [],
        isParsed: false,
        error: 'No parameters provided'
      };
    }

    try {
      const parsedArray = JSON.parse(projectParamsString);
      const parameterMap = {};
      
      parsedArray.forEach(param => {
        parameterMap[param.name] = param.value;
      });

      return {
        parsed: parsedArray,
        asMap: parameterMap,
        isParsed: true,
        count: parsedArray.length,
        parameterNames: parsedArray.map(p => p.name),
        rawString: projectParamsString
      };
    } catch (parseError) {
      console.error('Failed to parse custom parameters:', parseError);
      return {
        parsed: [],
        asMap: {},
        isParsed: false,
        error: parseError.message,
        rawString: projectParamsString
      };
    }
  }

  static buildSurveyQualificationTable(surveyList) {
    if (!Array.isArray(surveyList)) {
      return [];
    }

    return surveyList.map(survey => {
      const qualifications = SurveyQualificationsQuotaService.parseSurveyQualifications(survey);
      
      return {
        surveyId: survey.project_id,
        surveyNumber: survey.project_pno,
        surveyName: survey.project_name,
        quotaRemaining: qualifications.quotaStatus.remaining,
        quotaTotal: qualifications.quotaStatus.total,
        quotaPercentage: qualifications.quotaStatus.percentageFilled,
        duration: `${qualifications.durationMinutes} min`,
        incidenceRate: `${qualifications.incidenceRate}%`,
        reward: qualifications.rewardAmount.displayFormat,
        country: qualifications.countryCode,
        platform: qualifications.platformInfo.name,
        hasQuotaDetails: qualifications.platformInfo.hasDetailedQuota,
        requirements: qualifications.customQualifications.parsed.map(p => `${p.name}: ${p.value}`).join('; '),
        isPlatformFull: qualifications.quotaStatus.isFull,
        lastUpdated: qualifications.timestamps.updatedAt
      };
    });
  }

  static formatQuotaDisplay(quotaResponse) {
    if (!quotaResponse.success) {
      return {
        displayable: false,
        error: quotaResponse.error
      };
    }

    if (quotaResponse.isHtmlType) {
      return {
        displayable: true,
        type: 'HTML',
        content: quotaResponse.quotaContent,
        displayMethod: 'v-html directive in Vue',
        requiresSanitization: true,
        containerSelector: '.quota-html-content',
        maxHeight: '600px',
        overflow: 'auto'
      };
    }

    if (quotaResponse.isLinkType) {
      return {
        displayable: true,
        type: 'IFRAME',
        contentUrl: quotaResponse.quotaContent,
        displayMethod: 'iframe element',
        iframeAttributes: {
          src: quotaResponse.quotaContent,
          width: '100%',
          height: '600px',
          border: '0',
          style: 'margin: 0; padding: 0;'
        }
      };
    }

    return {
      displayable: false,
      error: 'Unknown quota type: ' + quotaResponse.quotaType
    };
  }

  static async fetchAndProcessSurveys(platformId) {
    const offersResponse = await SurveyQualificationsQuotaService.fetchSurveyOffers(platformId);

    if (!offersResponse.success) {
      return {
        success: false,
        error: offersResponse.error,
        processed: []
      };
    }

    const processedSurveys = offersResponse.surveyList.map(survey => {
      return SurveyQualificationsQuotaService.parseSurveyQualifications(survey);
    });

    return {
      success: true,
      totalSurveys: offersResponse.totalCount,
      surveys: processedSurveys,
      visibilityFlags: offersResponse.visibilityFlags,
      qualificationFieldsAvailable: Object.keys(processedSurveys[0] || {})
    };
  }

  static async fetchQuotaForSurvey(survey) {
    if (!survey.hasDetailedQuota && !survey.platform.is_quota) {
      return {
        success: false,
        error: 'This survey does not have detailed quota information',
        requiresApproval: true
      };
    }

    const quotaResponse = await SurveyQualificationsQuotaService.fetchQuotaDetails(survey.project_pno);
    const formattedQuota = SurveyQualificationsQuotaService.formatQuotaDisplay(quotaResponse);

    return {
      success: quotaResponse.success,
      survey: survey.project_name,
      quota: formattedQuota,
      rawQuotaData: quotaResponse,
      timestamp: new Date().toISOString()
    };
  }

  static getQualificationMetadata() {
    return {
      totalQualifications: 8,
      qualifications: {
        '1': {
          name: 'Quota Status',
          fields: ['project_quota', 'project_complete'],
          dataType: 'calculated from two integers',
          displayFormat: 'completed / total'
        },
        '2': {
          name: 'Project Name',
          fields: ['project_name'],
          dataType: 'string',
          displayFormat: 'text'
        },
        '3': {
          name: 'Country Code',
          fields: ['code'],
          dataType: 'string (2 letters)',
          displayFormat: 'US, UK, etc'
        },
        '4': {
          name: 'Length of Interview',
          fields: ['project_loi'],
          dataType: 'integer',
          displayFormat: 'N minutes',
          unit: 'minutes'
        },
        '5': {
          name: 'Incidence Rate',
          fields: ['project_ir'],
          dataType: 'integer (0-100)',
          displayFormat: 'N%',
          unit: 'percentage'
        },
        '6': {
          name: 'Completion Count',
          fields: ['project_complete'],
          dataType: 'integer',
          displayFormat: 'N respondents'
        },
        '7': {
          name: 'Reward Amount',
          fields: ['project_cpi'],
          dataType: 'integer',
          displayFormat: '$X.XX or N coins',
          note: 'divide by 100 for USD'
        },
        '8': {
          name: 'Custom Qualifications',
          fields: ['project_params'],
          dataType: 'JSON string (array)',
          displayFormat: 'parsed key-value table',
          requiresParsing: true
        }
      }
    };
  }

  static validateSurveyData(survey) {
    const validationResult = {
      isValid: true,
      errors: [],
      warnings: [],
      survey: survey.project_pno
    };

    if (!survey.project_quota || survey.project_quota <= 0) {
      validationResult.errors.push('Invalid quota: must be greater than 0');
      validationResult.isValid = false;
    }

    if (survey.project_complete > survey.project_quota) {
      validationResult.warnings.push('Completion count exceeds quota target');
    }

    if (!survey.project_name || survey.project_name.trim() === '') {
      validationResult.errors.push('Survey name is missing');
      validationResult.isValid = false;
    }

    if (!survey.project_loi || survey.project_loi <= 0) {
      validationResult.errors.push('Invalid LOI (Length of Interview)');
      validationResult.isValid = false;
    }

    if (survey.project_ir < 0 || survey.project_ir > 100) {
      validationResult.errors.push('Incidence rate must be between 0-100');
      validationResult.isValid = false;
    }

    if (!survey.project_cpi || survey.project_cpi < 0) {
      validationResult.errors.push('Invalid reward amount');
      validationResult.isValid = false;
    }

    if (survey.project_params) {
      try {
        JSON.parse(survey.project_params);
      } catch (e) {
        validationResult.errors.push('Custom parameters JSON is malformed: ' + e.message);
        validationResult.isValid = false;
      }
    }

    return validationResult;
  }
}

export default SurveyQualificationsQuotaService;
