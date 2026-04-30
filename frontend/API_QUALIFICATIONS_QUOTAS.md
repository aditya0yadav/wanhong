# API Documentation: Qualifications & Quotas

## Overview

This document provides complete API specifications for qualification and quota fetching in the survey system.

---

## 1. Get Platform Offers with Qualifications

### Endpoint Details

| Property | Value |
|----------|-------|
| **URL** | `/api/member.Platform/offers` |
| **Method** | `GET` |
| **Purpose** | Fetch list of survey offers with detailed qualification data |
| **Location** | `src/api/modules/platform.js` (line 8-10) |
| **Frontend Component** | `src/views/platOffers.vue` |

### Request Parameters

```javascript
{
  "page": 1,                    // (integer) Pagination page number, default: 1
  "limit": 20,                  // (integer) Items per page, default: 20
  "platform_id": "1",           // (string/integer) REQUIRED - Platform identifier
  "search_field": "project_name", // (string) Field to search in, default: "project_name"
  "search_exp": "like",         // (string) Search expression type: "like" | "exact"
  "sort_field": "update_time",  // (string) Sort field: "update_time" | "create_time" | "project_cpi"
  "sort_value": "desc",         // (string) Sort order: "asc" | "desc"
  "date_field": "create_time",  // (string) Date field for filtering
  "code": "US",                 // (string) OPTIONAL - Country code filter
  "project_pno": "PROJ-001",    // (string) OPTIONAL - Project number filter
  "project_name": "Survey"      // (string) OPTIONAL - Project name filter
}
```

### Response Structure

```json
{
  "status": 200,
  "data": {
    "list": [
      {
        "project_id": 12345,
        "project_pno": "PROJ-2024-001",
        "project_name": "Customer Satisfaction Survey 2024",
        "platform_id": 1,
        "platform": {
          "platform_id": 1,
          "platform_name": "SurveyHub",
          "is_quota": 1
        },
        "code": "US",
        "project_cpi": 150,
        "project_quota": 1000,
        "project_complete": 450,
        "project_loi": 15,
        "project_ir": 25,
        "project_params": "[{\"name\":\"age_group\",\"value\":\"18-25\"},{\"name\":\"gender\",\"value\":\"Female\"}]",
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
}
```

### Response Fields Explained

#### List Item Structure

| Field | Type | Description |
|-------|------|-------------|
| `project_id` | integer | Unique survey project ID |
| `project_pno` | string | Project number (unique identifier) |
| `project_name` | string | Survey title/name |
| `platform_id` | integer | Associated platform ID |
| `platform` | object | Platform details object |
| `code` | string | Country code (e.g., "US", "UK", "DE") |
| `project_cpi` | integer | Cost per interview in cents/coins |
| `project_quota` | integer | Total quota (max participants) |
| `project_complete` | integer | Current completed count |
| `project_loi` | integer | Length of Interview in minutes |
| `project_ir` | integer | Incidence Rate (0-100 percentage) |
| `project_params` | string | JSON string of custom qualifications |
| `create_time` | string | Timestamp (YYYY-MM-DD HH:mm:ss) |
| `update_time` | string | Last update timestamp |

#### Platform Object

```json
{
  "platform_id": 1,
  "platform_name": "SurveyHub",
  "is_quota": 1
}
```

| Field | Type | Description |
|-------|------|-------------|
| `platform_id` | integer | Platform identifier |
| `platform_name` | string | Platform display name |
| `is_quota` | integer | 1 = has detailed quota info, 0 = no detailed quota |

#### Response Flags

| Flag | Type | Description |
|------|------|-------------|
| `show_quota` | boolean | Display quota column in table |
| `show_name` | boolean | Display project name column |
| `show_loi` | boolean | Display LOI column |
| `show_ir` | boolean | Display incidence rate column |
| `show_complete` | boolean | Display completion count column |
| `show_click` | boolean | Display click count column |

### Custom Qualifications (project_params)

The `project_params` field contains a JSON string with dynamic qualification parameters:

```json
[
  {
    "name": "age_group",
    "value": "18-25"
  },
  {
    "name": "gender",
    "value": "Female"
  },
  {
    "name": "income_level",
    "value": "$50k-$100k"
  },
  {
    "name": "employment_status",
    "value": "Employed Full-Time"
  }
]
```

**Processing in Frontend:**
```javascript
// Parse the JSON string
item.project_params = JSON.parse(item.project_params);

// Extract unique parameter names for table columns
const customParams = [];
item.project_params.forEach(param => {
  customParams.push(param.name);
});
```

### Frontend Usage Example

```javascript
import { getOffers } from '@/api/modules/platform'

const query = {
  page: 1,
  limit: 20,
  platform_id: 1,
  sort_field: 'update_time',
  sort_value: 'desc'
};

getOffers(query).then(res => {
  const { list, count, show_quota, show_name, show_loi, show_ir, show_complete } = res.data;
  
  // Process offers
  list.forEach(offer => {
    // Parse custom qualifications
    offer.project_params = JSON.parse(offer.project_params);
  });
  
  // Use visibility flags to control UI
  console.log('Show quota:', show_quota);
  console.log('Show LOI:', show_loi);
});
```

---

## 2. Get Detailed Quota Information

### Endpoint Details

| Property | Value |
|----------|-------|
| **URL** | `/api/member.Platform/quota` |
| **Method** | `GET` |
| **Purpose** | Fetch detailed quota status/information for a specific survey |
| **Location** | `src/api/modules/platform.js` (line 24-26) |
| **Frontend Component** | `src/views/platOffers.vue` |
| **Trigger** | User clicks "Quota" button on offer row (only if `platform.is_quota == 1`) |

### Request Parameters

```javascript
{
  "project_pno": "PROJ-2024-001"  // (string) REQUIRED - Project number from offer
}
```

### Response Structure

```json
{
  "status": 200,
  "data": {
    "type": "html",
    "content": "<div class=\"quota-info\"><h2>Quota Details</h2><table><tr><td>Age Group</td><td>18-25</td></tr><tr><td>Gender</td><td>Female</td></tr><tr><td>Remaining Slots</td><td>250 / 500</td></tr></table></div>"
  }
}
```

### Response Field Options

#### Type: HTML
```json
{
  "type": "html",
  "content": "<h2>Quota Status</h2><p>Remaining: 300/500</p><p>Qualification: Age 18-25, Female</p>"
}
```

The HTML content is displayed in a modal using Vue's `v-html` directive.

#### Type: Link (URL)
```json
{
  "type": "link",
  "content": "https://external-platform.com/quota/survey123"
}
```

The URL is rendered in an iframe within the modal.

### Response Fields

| Field | Type | Values |
|-------|------|--------|
| `type` | string | `"html"` or `"link"` |
| `content` | string | HTML markup or URL depending on type |

### Frontend Usage Example

```javascript
import { getQuota } from '@/api/modules/platform'

// Trigger: User clicks quota button
const toQuota = (offer) => {
  quotaVisible.value = true;
  quotaLoading.value = true;
  
  getQuota({ project_pno: offer.project_pno })
    .then(res => {
      const { type, content } = res.data;
      quotaLoading.value = false;
      
      if (type === 'link') {
        // Display in iframe
        quotaUrl.value = content;
        quotaType.value = 'iframe';
      } else if (type === 'html') {
        // Display HTML content
        quotaContent.value = content;
        quotaType.value = 'html';
      }
    })
};

// In template:
// <iframe v-if="quotaType === 'iframe'" :src="quotaUrl" />
// <div v-else-if="quotaType === 'html'" v-html="quotaContent" />
```

---

## 3. Get Platform List

### Endpoint Details

| Property | Value |
|----------|-------|
| **URL** | `/api/member.Platform/list` |
| **Method** | `GET` |
| **Purpose** | Fetch list of all available survey platforms |
| **Location** | `src/api/modules/platform.js` (line 3-5) |
| **Frontend Component** | `src/views/offers.vue` |

### Request Parameters

None. No parameters required.

```javascript
{}
```

### Response Structure

```json
{
  "status": 200,
  "data": [
    {
      "platform_id": 1,
      "platform_name": "SurveyHub",
      "platform_color": "#FF6B6B",
      "logo_url": "https://assets.example.com/surveyhub-logo.png",
      "is_wall": 1,
      "is_list": 1
    },
    {
      "platform_id": 2,
      "platform_name": "QuestionBank",
      "platform_color": "#4ECDC4",
      "logo_url": "https://assets.example.com/questionbank-logo.png",
      "is_wall": 0,
      "is_list": 1
    }
  ]
}
```

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `platform_id` | integer | Unique platform identifier |
| `platform_name` | string | Platform display name |
| `platform_color` | string | Hex color code for UI theming |
| `logo_url` | string | URL to platform logo image |
| `is_wall` | integer | 1 = Show in discount wall section, 0 = Hide |
| `is_list` | integer | 1 = Show in partner list section, 0 = Hide |

### Frontend Usage Example

```javascript
import { getPlatformList } from '@/api/modules/platform'

getPlatformList().then(res => {
  const platforms = res.data;
  
  // Separate into walls and lists
  const walls = platforms.filter(p => p.is_wall === 1);
  const lists = platforms.filter(p => p.is_list === 1);
  
  // Use in UI
  walls.forEach(wall => {
    console.log(`Wall: ${wall.platform_name} (${wall.platform_color})`);
  });
});
```

---

## 4. Get Featured Offers

### Endpoint Details

| Property | Value |
|----------|-------|
| **URL** | `/api/member.Platform/featured` |
| **Method** | `GET` |
| **Purpose** | Fetch featured/trending survey offers |
| **Location** | `src/api/modules/platform.js` (line 11-13) |
| **Frontend Component** | `src/views/offers.vue` |

### Request Parameters

None. No parameters required.

```javascript
{}
```

### Response Structure

```json
{
  "status": 200,
  "data": [
    {
      "project_id": 99,
      "project_pno": "FEAT-001",
      "project_name": "Featured Survey - High Reward",
      "project_cpi": 500,
      "rating": 5
    },
    {
      "project_id": 100,
      "project_pno": "FEAT-002",
      "project_name": "Popular Survey - Quick",
      "project_cpi": 200,
      "rating": 4.5
    }
  ]
}
```

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `project_id` | integer | Survey project ID |
| `project_pno` | string | Project number |
| `project_name` | string | Survey title |
| `project_cpi` | integer | Reward in cents/coins |
| `rating` | number | User rating (0-5) |

---

## 5. Qualification Fields Summary

### All Qualifications Tracked

| ID | Name | API Field | Type | Source | Display Format |
|----|------|-----------|------|--------|-----------------|
| QF-001 | Quota Status | `project_quota`, `project_complete` | Integer | `/offers` | "completed/total" |
| QF-002 | Project Name | `project_name` | String | `/offers` | Text |
| QF-003 | Country Code | `code` | String | `/offers` | "US", "UK", etc |
| QF-004 | LOI (Minutes) | `project_loi` | Integer | `/offers` | "15" |
| QF-005 | Incidence Rate | `project_ir` | Integer | `/offers` | "25%" |
| QF-006 | Completion Count | `project_complete` | Integer | `/offers` | "450" |
| QF-007 | Reward Amount | `project_cpi` | Integer | `/offers` | "150 coins" or "$1.50" |
| QF-008 | Custom Parameters | `project_params` | JSON Array | `/offers` | Key-value table |

### Visibility Control

These qualifications have visibility flags returned by `/offers`:

```javascript
{
  "show_quota": true,      // Show/hide quota column
  "show_name": true,       // Show/hide name column
  "show_loi": true,        // Show/hide LOI column
  "show_ir": true,         // Show/hide IR column
  "show_complete": true,   // Show/hide completion column
  "show_click": true       // Show/hide click count column
}
```

---

## 6. Survey-Specific API Implementation Details

### Survey Qualification Validation Flow

```
1. User navigates to platform page
   │
   ├─ Call: GET /api/member.Platform/offers
   │   └─ Response includes all qualification fields
   │   └─ Visibility flags determine column display
   │
2. System displays offers with qualifications
   │
   ├─ Parse project_params JSON for custom qualifications
   │ ├─ Age Group
   │ ├─ Gender
   │ ├─ Employment Status
   │ ├─ Income Level
   │ └─ Other survey-specific parameters
   │
3. Check if offer has detailed quota (is_quota == 1)
   │
   ├─ If yes: Show "Quota" link
   │ └─ User clicks → Call: GET /api/member.Platform/quota
   │     └─ Response: HTML or iframe with quota details
   │
4. Display complete offer with all qualifications
```

### Quota Details Types

#### HTML Type Response
- Contains formatted HTML with quota breakdown
- Displays remaining slots vs total quota
- Shows qualification requirements
- Rendered within modal using v-html (unsafe if not sanitized)

#### Link Type Response
- External URL to quota details page
- Rendered in iframe within modal
- Better for third-party quota management systems

### Qualification Data Mapping for Survey

```javascript
// API Response → Qualification Display
{
  "project_quota": 1000,        // Total survey slots needed
  "project_complete": 450,      // Currently completed
  "project_loi": 15,            // Expected survey duration
  "project_ir": 25,             // Expected completion rate
  "project_params": [           // Survey-specific requirements
    { "name": "age", "value": "18-35" },
    { "name": "education", "value": "Bachelor's+" },
    { "name": "occupation", "value": "IT Professional" }
  ]
}
```

### Survey Progress Calculation

```javascript
// Calculate remaining quota
const remaining = offer.project_quota - offer.project_complete;
const percentFilled = (offer.project_complete / offer.project_quota) * 100;

// Example: 450/1000 = 45% filled, 550 remaining
console.log(`Progress: ${percentFilled}% (${remaining} slots left)`);
```

### Custom Qualification Processing

```javascript
// Extract survey-specific qualifications
const survey = {
  id: offer.project_pno,
  name: offer.project_name,
  qualifications: {}
};

// Parse custom parameters
offer.project_params.forEach(param => {
  survey.qualifications[param.name] = param.value;
});

// Result:
// {
//   qualifications: {
//     age: "18-35",
//     education: "Bachelor's+",
//     occupation: "IT Professional"
//   }
// }
```

---

## 7. API Call Sequence Diagram

```
Frontend Flow:
────────────────────────────────────────────────────────

Entry Point: offers.vue
    │
    ├─ 1. GET /api/member.Platform/featured
    │      └─ Featured surveys carousel
    │
    └─ 2. GET /api/member.Platform/list
         └─ Platform list (walls & lists)

User navigates to platform
    │
    └─ 3. GET /api/member.Platform/offers
         ├─ Platform ID: route.params.id
         ├─ Query: page, limit, sort, filters
         └─ Response: offers with ALL qualifications + visibility flags
            ├─ project_quota / project_complete (quota)
            ├─ project_name (name)
            ├─ project_loi (LOI minutes)
            ├─ project_ir (incidence rate %)
            ├─ project_cpi (reward)
            ├─ project_params (custom qualifications)
            └─ show_* flags (visibility control)

User clicks "Quota" button (if is_quota == 1)
    │
    └─ 4. GET /api/member.Platform/quota
         ├─ Parameter: project_pno
         └─ Response: type (html|link) + content
            └─ Display in modal
```

---

## 8. HTTP Configuration

```javascript
// src/api/index.js
const config = {
  baseURL: 'http://localhost:8000',
  timeout: 300000,  // 5 minutes
  headers: {
    'Content-Type': 'application/json'
  }
};
```

---

## 9. Error Handling Recommendations

### Expected Status Codes

| Code | Meaning | Action |
|------|---------|--------|
| 200 | Success | Process response |
| 400 | Bad Request | Invalid parameters |
| 401 | Unauthorized | Redirect to login |
| 403 | Forbidden | User lacks permissions |
| 404 | Not Found | Offer/quota doesn't exist |
| 500 | Server Error | Retry or show error message |

### Common Scenarios

```javascript
// Invalid platform_id
GET /api/member.Platform/offers?platform_id=999
→ 404 or empty list

// Missing required parameter
GET /api/member.Platform/quota
(no project_pno)
→ 400 Bad Request

// Server timeout
→ After 300 seconds, connection aborts
```

---

## 10. Example Integration Code

### Complete Survey Offer Loading

```javascript
import { getOffers, getQuota, getPlatformList } from '@/api/modules/platform'

export const loadSurveyOffers = async (platformId) => {
  try {
    // 1. Fetch platform list
    const platforms = await getPlatformList();
    const currentPlatform = platforms.find(p => p.platform_id === platformId);

    // 2. Fetch offers with qualifications
    const offersResponse = await getOffers({
      page: 1,
      limit: 20,
      platform_id: platformId,
      sort_field: 'update_time',
      sort_value: 'desc'
    });

    // 3. Process offers
    const offers = offersResponse.data.list.map(offer => ({
      id: offer.project_pno,
      name: offer.project_name,
      reward: offer.project_cpi,
      
      // Qualifications
      quota: {
        total: offer.project_quota,
        completed: offer.project_complete,
        remaining: offer.project_quota - offer.project_complete
      },
      loi: offer.project_loi,
      ir: offer.project_ir,
      country: offer.code,
      
      // Custom qualifications
      requirements: offer.project_params ? 
        JSON.parse(offer.project_params) : [],
      
      // Quota details available
      hasDetailedQuota: offer.platform.is_quota === 1
    }));

    return {
      platform: currentPlatform,
      offers: offers,
      visibilityFlags: {
        quota: offersResponse.data.show_quota,
        name: offersResponse.data.show_name,
        loi: offersResponse.data.show_loi,
        ir: offersResponse.data.show_ir,
        complete: offersResponse.data.show_complete
      }
    };
  } catch (error) {
    console.error('Failed to load survey offers:', error);
    throw error;
  }
};

// Usage:
loadSurveyOffers(1).then(data => {
  console.log(`Platform: ${data.platform.platform_name}`);
  console.log(`Offers: ${data.offers.length}`);
  data.offers.forEach(offer => {
    console.log(`- ${offer.name}: ${offer.reward} coins`);
    console.log(`  Quota: ${offer.quota.completed}/${offer.quota.total}`);
    console.log(`  Requirements:`, offer.requirements);
  });
});
```

---

**Document Version:** 1.0  
**Last Updated:** March 2026  
**Scope:** API-only specifications for qualifications and quotas
