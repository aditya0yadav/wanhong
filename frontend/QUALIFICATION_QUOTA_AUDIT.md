# Qualification & Quota Fetching - Detailed Audit Report

## Executive Summary
This document provides a comprehensive analysis of how the application fetches and handles **qualifications** and **quotas** across the frontend system.

---

## 1. API Endpoints Overview

### 1.1 Quota Fetching
**Endpoint:** `/api/member.Platform/quota`
**File:** `src/api/modules/platform.js` (Line 24-26)

```javascript
export const getQuota = (params) => {
    return http.get(`/api/member.Platform/quota`, params,{loading: false});
}
```

**Parameters Passed:**
- `project_pno` (Project Number)

**Response Structure:**
```json
{
  "type": "link|html",
  "content": "URL or HTML content"
}
```

---

### 1.2 Offers Fetching (with Qualification Data)
**Endpoint:** `/api/member.Platform/offers`
**File:** `src/api/modules/platform.js` (Line 8-10)

```javascript
export const getOffers = (params) => {
    return http.get(`/api/member.Platform/offers`, params,{ loading: false });
}
```

**Parameters Passed:**
```javascript
{
  page: 1,
  limit: 20,
  search_field: 'project_name',
  search_exp: 'like',
  date_field: 'create_time',
  platform_id: route.params.id,
  code: '',
  project_pno: '',
  project_name: '',
  sort_field: 'update_time',
  sort_value: 'desc'
}
```

**Response Structure:**
```json
{
  "list": [
    {
      "project_pno": "12345",
      "project_name": "Survey Title",
      "project_cpi": 100,
      "project_quota": 500,
      "project_complete": 200,
      "project_params": "[{\"name\": \"param_name\", \"value\": \"param_value\"}]",
      "platform": {
        "is_quota": 1
      }
    }
  ],
  "show_quota": true,
  "show_name": true,
  "show_loi": true,
  "show_ir": true,
  "show_complete": true,
  "show_click": true,
  "count": 100
}
```

---

## 2. Frontend Components & Data Flow

### 2.1 Entry Point: offers.vue
**Location:** `src/views/offers.vue`

**Data Fetched:**
1. Featured offers via `getFeatured()`
2. Platform list via `getPlatformList()` 
3. Filters walls and lists by `is_wall` and `is_list` properties

```javascript
getPlatformList().then(res => {
    const data = res.data;
    data.forEach(item => {
        if (item.is_wall == 1) {
            walls.value.push(item)
        }
        if (item.is_list == 1) {
            lists.value.push(item)
        }
    });
    loading.value = false
})
```

---

### 2.2 Platform Detail Page: platOffers.vue
**Location:** `src/views/platOffers.vue`

#### 2.2.1 Qualification Display Logic
The application shows qualification fields based on response flags:

```javascript
const getData = () => {
    tableData.value = [];
    loading.value = true;
    getOffers(query.value).then(res => {
        const data = res.data.list;
        showQuota.value = res.data.show_quota ? true : false;
        showName.value = res.data.show_name ? true : false;
        showLoi.value = res.data.show_loi ? true : false;
        showIr.value = res.data.show_ir ? true : false;
        showComplete.value = res.data.show_complete ? true : false;
        showClick.value = res.data.show_click ? true : false;
        // ... process data
    })
}
```

**Displayed Qualifications:**
| Field | Component | Source |
|-------|-----------|--------|
| Quota Status | `project_quota` / `project_complete` | Response data |
| Project Name | Text display | Response data |
| Country Code | Custom field | Response data |
| LOI (Length of Interview) | Custom column | Response data |
| IR (Incidence Rate) | Custom column | Response data |
| Reward Coins | Currency display | `project_cpi` field |

#### 2.2.2 Quota Detail Fetching
```javascript
const toQuota = (item) => {
    quotaVisible.value = true;
    quotaLoading.value = true;
    quotaTitle.value = item.project_name;
    quota.value = '';
    getQuota({ project_pno: item.project_pno }).then(res => {
        const data = res.data || {};
        quotaLoading.value = false;
        nextTick(() => {
            if (data.type == 'link') {
                quotaType.value = 'link';
                quota.value = data.content;
            } else {
                quotaType.value = 'html';
                quota.value = data.content;
            }
        })
    })
}
```

---

## 3. Audit JSON Schema

### 3.1 Qualification Audit Schema
```json
{
  "audit_id": "QUAL-001",
  "component": "platOffers.vue",
  "endpoint": "/api/member.Platform/offers",
  "method": "GET",
  "qualifications_tracked": [
    {
      "id": "QF-001",
      "name": "Quota Display",
      "field": "show_quota",
      "visibility_flag": true,
      "data_source": "response.show_quota",
      "component_display": "el-table-column prop='project_quota'",
      "format": "Complete/Quota (e.g., 200/500)"
    },
    {
      "id": "QF-002",
      "name": "Project Name",
      "field": "show_name",
      "visibility_flag": true,
      "data_source": "response.show_name",
      "component_display": "el-table-column prop='project_name'",
      "format": "Text"
    },
    {
      "id": "QF-003",
      "name": "Country Code",
      "field": "code",
      "visibility_flag": true,
      "data_source": "response_list.item.code",
      "component_display": "el-table-column",
      "format": "Text (e.g., US, UK)"
    },
    {
      "id": "QF-004",
      "name": "LOI (Length of Interview)",
      "field": "show_loi",
      "visibility_flag": true,
      "data_source": "response.show_loi",
      "component_display": "el-table-column (conditional)",
      "format": "Integer (minutes)"
    },
    {
      "id": "QF-005",
      "name": "IR (Incidence Rate)",
      "field": "show_ir",
      "visibility_flag": true,
      "data_source": "response.show_ir",
      "component_display": "el-table-column (conditional)",
      "format": "Percentage (0-100)"
    },
    {
      "id": "QF-006",
      "name": "Completion Status",
      "field": "show_complete",
      "visibility_flag": true,
      "data_source": "response.show_complete",
      "component_display": "el-table-column (conditional)",
      "format": "Integer (completed count)"
    }
  ],
  "custom_params": {
    "description": "Dynamic qualifications from project params",
    "data_source": "response_list.item.project_params",
    "processing": "JSON.parse(project_params)",
    "structure": [
      {
        "name": "parameter_name",
        "value": "parameter_value"
      }
    ]
  }
}
```

### 3.2 Quota Audit Schema
```json
{
  "audit_id": "QUOTA-001",
  "component": "platOffers.vue",
  "endpoint": "/api/member.Platform/quota",
  "method": "GET",
  "trigger": "User clicks 'Quota' button on offer row",
  "quota_details": {
    "id": "Q-001",
    "request_parameters": {
      "project_pno": "string (required)"
    },
    "response_structure": {
      "type": "link|html",
      "content": "URL or HTML content string"
    },
    "display_handling": {
      "type_link": {
        "rendering": "iframe",
        "height": "700px",
        "width": "100%"
      },
      "type_html": {
        "rendering": "v-html directive",
        "sanitization": "None detected (potential XSS risk)"
      }
    },
    "modal_configuration": {
      "width": "1200px",
      "title": "project_name",
      "closeable": true,
      "loading_indicator": "el-progress"
    }
  }
}
```

### 3.3 Data Flow Audit
```json
{
  "audit_id": "FLOW-001",
  "data_flow": [
    {
      "step": 1,
      "description": "User navigates to offers page",
      "component": "offers.vue",
      "action": "Page loads"
    },
    {
      "step": 2,
      "description": "Fetch platform list and qualifications overview",
      "api_call": "getPlatformList()",
      "endpoint": "/api/member.Platform/list",
      "data_returned": ["platform_id", "platform_name", "is_wall", "is_list", "platform_color", "logo_url"]
    },
    {
      "step": 3,
      "description": "User clicks on platform to view offers",
      "component": "platOffers.vue",
      "router_param": "platform_id"
    },
    {
      "step": 4,
      "description": "Fetch offers with qualifications",
      "api_call": "getOffers(query)",
      "query_parameters": {
        "platform_id": "from router params",
        "page": "pagination",
        "limit": 20,
        "sort_field": "update_time|create_time|project_cpi",
        "sort_value": "desc|asc"
      },
      "data_returned": {
        "list": "array of offers with qualifications",
        "show_*": "visibility flags for each qualification field",
        "count": "total offers"
      }
    },
    {
      "step": 5,
      "description": "User clicks 'Quota' link on specific offer",
      "component": "platOffers.vue",
      "trigger_condition": "platform.is_quota == 1"
    },
    {
      "step": 6,
      "description": "Fetch quota details",
      "api_call": "getQuota()",
      "endpoint": "/api/member.Platform/quota",
      "parameters": {
        "project_pno": "from selected offer"
      }
    },
    {
      "step": 7,
      "description": "Display quota in modal",
      "display_type": "link|html",
      "content": "iframe or v-html based on type"
    }
  ]
}
```

### 3.4 Response Data Sample
```json
{
  "audit_id": "SAMPLE-001",
  "offers_response": {
    "status": "success",
    "data": {
      "list": [
        {
          "project_id": 123456,
          "project_pno": "PROJ-2024-001",
          "project_name": "Customer Satisfaction Survey",
          "platform_id": 1,
          "platform": {
            "platform_id": 1,
            "platform_name": "SurveySite",
            "is_quota": 1
          },
          "code": "US",
          "project_cpi": 150,
          "project_quota": 1000,
          "project_complete": 450,
          "project_loi": 15,
          "project_ir": 25,
          "project_params": "[{\"name\":\"age_group\",\"value\":\"18-25\"},{\"name\":\"income\",\"value\":\"$50k-$100k\"}]",
          "create_time": "2024-01-15 10:30:00",
          "update_time": "2024-01-20 14:45:00"
        }
      ],
      "count": 150,
      "show_quota": true,
      "show_name": true,
      "show_loi": true,
      "show_ir": true,
      "show_complete": true,
      "show_click": true
    }
  },
  "quota_response": {
    "status": "success",
    "data": {
      "type": "html",
      "content": "<div><h2>Quota Details</h2><p>Age: 18-25</p><p>Gender: Female</p><p>Remaining: 300/500</p></div>"
    }
  }
}
```

---

## 4. Potential Issues & Risks

### 4.1 Security Issues
```json
{
  "security_audit": [
    {
      "id": "SEC-001",
      "issue": "XSS Vulnerability in Quota Display",
      "severity": "HIGH",
      "location": "platOffers.vue - quotaType == 'html'",
      "code": "<div class=\"html\" v-html=\"quota\"></div>",
      "risk": "If quota.content contains malicious HTML/JavaScript, it will be executed",
      "recommendation": "Implement HTML sanitization using DOMPurify or similar library"
    },
    {
      "id": "SEC-002",
      "issue": "No Loading Error Handling",
      "severity": "MEDIUM",
      "location": "platOffers.vue - getQuota() promise",
      "risk": "If API fails, user sees stuck loading state",
      "recommendation": "Add .catch() handler to reset quotaLoading"
    },
    {
      "id": "SEC-003",
      "issue": "No Data Validation",
      "severity": "MEDIUM",
      "location": "getData() - project_params JSON parsing",
      "code": "JSON.parse(item.project_params)",
      "risk": "Invalid JSON will throw uncaught error",
      "recommendation": "Wrap in try-catch block"
    }
  ]
}
```

### 4.2 Performance Issues
```json
{
  "performance_audit": [
    {
      "id": "PERF-001",
      "issue": "No Pagination Optimization",
      "severity": "MEDIUM",
      "description": "Loading 20 offers per page with complex data structure",
      "recommendation": "Consider virtual scrolling for large lists"
    },
    {
      "id": "PERF-002",
      "issue": "No API Response Caching",
      "severity": "LOW",
      "description": "getPlatformList() called multiple times without caching",
      "recommendation": "Cache response at store level"
    }
  ]
}
```

---

## 5. API Integration Summary

### 5.1 HTTP Client Configuration
**File:** `src/api/index.js`

```json
{
  "http_config": {
    "baseURL": "http://localhost:8000",
    "timeout": 300000,
    "default_headers": {}
  }
}
```

### 5.2 Request/Response Interceptors
The application uses custom axios instance with:
- Full screen loading indicator
- Status code checking via `checkStatus()`
- User store integration for auth

---

## 6. Related Components & Modules

```json
{
  "related_files": [
    {
      "file": "src/api/modules/platform.js",
      "functions": [
        "getPlatformList",
        "getOffers",
        "getQuota",
        "getFeatured"
      ]
    },
    {
      "file": "src/views/offers.vue",
      "description": "Entry point for offers system"
    },
    {
      "file": "src/views/platOffers.vue",
      "description": "Detailed offers with qualifications and quotas"
    },
    {
      "file": "src/views/statistics.vue",
      "description": "Reward statistics with platform filtering"
    },
    {
      "file": "src/stores/modules/user.js",
      "description": "User authentication and platform info"
    }
  ]
}
```

---

## 7. Recommendations

### 7.1 Code Improvements
1. **Add Error Handling**
   ```javascript
   getQuota({ project_pno: item.project_pno }).then(res => {
       // ... existing code
   }).catch(err => {
       quotaLoading.value = false;
       ElMessage.error('Failed to load quota details');
   })
   ```

2. **Sanitize HTML Content**
   ```javascript
   import DOMPurify from 'dompurify';
   
   if (data.type == 'html') {
       quota.value = DOMPurify.sanitize(data.content);
   }
   ```

3. **Safe JSON Parsing**
   ```javascript
   try {
       item.project_params = item.project_params ? JSON.parse(item.project_params) : [];
   } catch(e) {
       console.error('Invalid project_params JSON', e);
       item.project_params = [];
   }
   ```

### 7.2 Testing Checklist
- [ ] Test quota loading with missing project_pno
- [ ] Test HTML quota response for XSS vulnerabilities
- [ ] Test error states when API returns 500
- [ ] Test with malformed JSON in project_params
- [ ] Test pagination with filters
- [ ] Test timezone handling for dates

---

## Appendix: Complete API Call Sequence Diagram

```
User
  ↓
offers.vue (Entry)
  ├─→ getFeatured() → Featured offers
  ├─→ getPlatformList() → Platforms (walls & lists)
  
User clicks platform
  ↓
platOffers.vue (Detail)
  ├─→ getData()
  │   ├─→ getOffers(query) → List of offers with qualifications
  │   └─→ Parse project_params for each offer
  │
  User clicks "Quota" button
  └─→ toQuota()
      └─→ getQuota({ project_pno }) → Quota details
          └─→ Display in modal (iframe or v-html)
```

---

**Report Generated:** 2024
**Audit Scope:** Frontend Qualification & Quota Fetching System
**Status:** Complete
