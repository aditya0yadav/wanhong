# Survey API Quick Reference

## Endpoints Overview

| # | Endpoint | Method | Purpose | Survey |
|---|----------|--------|---------|--------|
| 1 | `/api/member.Platform/offers` | GET | Get survey list with **qualifications** | ✓ YES |
| 2 | `/api/member.Platform/quota` | GET | Get detailed **quota info** | ✓ YES |
| 3 | `/api/member.Platform/list` | GET | Get available platforms | ✗ NO |
| 4 | `/api/member.Platform/featured` | GET | Get featured surveys | ✓ YES |

---

## 1️⃣ Get Survey Offers (Most Used)

### Request
```
GET /api/member.Platform/offers?platform_id=1&page=1&limit=20&sort_field=update_time&sort_value=desc
```

### Response
```json
{
  "list": [
    {
      "project_pno": "PROJ-001",
      "project_name": "Brand Survey",
      "project_quota": 1000,
      "project_complete": 450,
      "project_loi": 15,
      "project_ir": 25,
      "project_cpi": 150,
      "project_params": "[{\"name\":\"age\",\"value\":\"18-25\"}]"
    }
  ],
  "count": 100,
  "show_quota": true,
  "show_loi": true,
  "show_ir": true
}
```

### Code
```javascript
import { getOffers } from '@/api/modules/platform'

const response = await getOffers({
  platform_id: 1,
  page: 1,
  limit: 20
})

// Process surveys
response.data.list.forEach(survey => {
  console.log(survey.project_name)
  console.log(`Quota: ${survey.project_complete}/${survey.project_quota}`)
  console.log(`Duration: ${survey.project_loi} min`)
  console.log(`IR: ${survey.project_ir}%`)
  console.log(`Reward: $${survey.project_cpi/100}`)
})
```

### Survey Qualifications Returned
✅ Quota (total/completed)  
✅ Project Name  
✅ Country Code  
✅ LOI (Duration in minutes)  
✅ IR (Incidence Rate %)  
✅ Reward Amount  
✅ Custom Parameters (JSON)  

---

## 2️⃣ Get Detailed Quota

### Request
```
GET /api/member.Platform/quota?project_pno=PROJ-001
```

### Response (HTML Type)
```json
{
  "type": "html",
  "content": "<h2>Quota Details</h2><p>Remaining: 550/1000</p>"
}
```

### Response (Link Type)
```json
{
  "type": "link",
  "content": "https://quota-system.com/survey/PROJ-001"
}
```

### Code
```javascript
import { getQuota } from '@/api/modules/platform'

const response = await getQuota({
  project_pno: 'PROJ-001'
})

if (response.data.type === 'html') {
  // Display HTML
  quotaContent.value = response.data.content
} else if (response.data.type === 'link') {
  // Show in iframe
  quotaUrl.value = response.data.content
}
```

---

## 3️⃣ Get Platform List

### Request
```
GET /api/member.Platform/list
```

### Response
```json
[
  {
    "platform_id": 1,
    "platform_name": "SurveyHub",
    "platform_color": "#FF6B6B",
    "logo_url": "https://...",
    "is_wall": 1,
    "is_list": 1
  }
]
```

### Code
```javascript
import { getPlatformList } from '@/api/modules/platform'

const response = await getPlatformList()
const platforms = response.data

// Filter walls and lists
const walls = platforms.filter(p => p.is_wall === 1)
const lists = platforms.filter(p => p.is_list === 1)
```

---

## 4️⃣ Get Featured Surveys

### Request
```
GET /api/member.Platform/featured
```

### Response
```json
[
  {
    "project_pno": "FEAT-001",
    "project_name": "High Reward Survey",
    "project_cpi": 500,
    "rating": 4.5
  }
]
```

### Code
```javascript
import { getFeatured } from '@/api/modules/platform'

const response = await getFeatured()
const featured = response.data
```

---

## Parameters Quick Lookup

### For `/api/member.Platform/offers`

| Parameter | Type | Default | Options |
|-----------|------|---------|---------|
| `platform_id` | string | REQUIRED | - |
| `page` | int | 1 | 1+ |
| `limit` | int | 20 | 10-100 |
| `sort_field` | string | `update_time` | `update_time`, `create_time`, `project_cpi` |
| `sort_value` | string | `desc` | `asc`, `desc` |
| `code` | string | - | Country code |
| `project_name` | string | - | Search text |
| `project_pno` | string | - | Project number |

---

## Response Fields Guide

### Survey Object Fields

| Field | Type | Notes |
|-------|------|-------|
| `project_pno` | string | Use for quota API calls |
| `project_name` | string | Survey title |
| `project_quota` | int | Total slots |
| `project_complete` | int | Completed count |
| `project_loi` | int | Minutes |
| `project_ir` | int | Percentage (0-100) |
| `project_cpi` | int | Cents or coins |
| `project_params` | string | JSON - parse it! |
| `code` | string | Country code |
| `platform.is_quota` | int | 1=has quota details |

### Visibility Flags

```json
{
  "show_quota": true,
  "show_name": true,
  "show_loi": true,
  "show_ir": true,
  "show_complete": true,
  "show_click": true
}
```

Use these to control which columns display in your table.

---

## Common Calculations

### Quota Remaining
```javascript
const remaining = survey.project_quota - survey.project_complete
const percentFilled = (survey.project_complete / survey.project_quota) * 100
```

### Currency Conversion
```javascript
const usd = survey.project_cpi / 100
const displayText = `$${usd.toFixed(2)}`
```

### Parse Custom Qualifications
```javascript
const params = JSON.parse(survey.project_params)
// Returns: [{name: "age", value: "18-25"}, ...]
```

---

## Import & Usage Template

```javascript
// 1. Import functions
import { 
  getOffers, 
  getQuota, 
  getPlatformList, 
  getFeatured 
} from '@/api/modules/platform'

// 2. Define refs
const surveys = ref([])
const selectedSurvey = ref(null)
const quotaDetails = ref(null)

// 3. Load surveys
const loadSurveys = async (platformId) => {
  try {
    const res = await getOffers({
      platform_id: platformId,
      page: 1,
      limit: 20
    })
    surveys.value = res.data.list
    console.log(`Loaded ${surveys.value.length} surveys`)
  } catch (err) {
    console.error('Error:', err)
  }
}

// 4. Show quota details
const showQuota = async (survey) => {
  try {
    const res = await getQuota({ project_pno: survey.project_pno })
    quotaDetails.value = res.data
  } catch (err) {
    console.error('Error:', err)
  }
}

// 5. Use in template
// <div v-for="survey in surveys">
//   <h3>{{ survey.project_name }}</h3>
//   <p>Quota: {{ survey.project_complete }}/{{ survey.project_quota }}</p>
//   <p>Reward: ${{ (survey.project_cpi/100).toFixed(2) }}</p>
//   <button @click="showQuota(survey)">View Quota</button>
// </div>
```

---

## Error Handling

```javascript
const loadSurveys = async (platformId) => {
  try {
    const res = await getOffers({ platform_id: platformId })
    surveys.value = res.data.list
  } catch (error) {
    console.error('Failed to load surveys:', error)
    surveys.value = []
    // Show error message to user
  }
}
```

---

## Data Transformation Examples

### Simple Survey Card
```javascript
const surveyCard = {
  title: survey.project_name,
  reward: `$${(survey.project_cpi/100).toFixed(2)}`,
  duration: `${survey.project_loi} min`,
  completion: `${survey.project_complete}/${survey.project_quota}`,
  percentage: (survey.project_complete / survey.project_quota) * 100
}
```

### Qualification Badge
```javascript
const params = JSON.parse(survey.project_params)
const badges = params.map(p => `${p.name}: ${p.value}`)
// Output: ["age: 18-25", "gender: Female", ...]
```

### Survey Summary
```javascript
const summary = {
  name: survey.project_name,
  available: survey.project_quota > survey.project_complete,
  remaining: survey.project_quota - survey.project_complete,
  reward: survey.project_cpi,
  timeRequired: survey.project_loi,
  successRate: survey.project_ir
}
```

---

## Testing Quick Commands

```javascript
// In browser console:

// Test 1: Load surveys
const res = await getOffers({platform_id: 1, page: 1, limit: 5})
console.log('Surveys:', res.data.list)

// Test 2: Get quota details
const quota = await getQuota({project_pno: 'PROJ-001'})
console.log('Quota:', quota.data)

// Test 3: Get platforms
const platforms = await getPlatformList()
console.log('Platforms:', platforms.data)

// Test 4: Get featured
const featured = await getFeatured()
console.log('Featured:', featured.data)
```

---

## File Locations

| File | Content |
|------|---------|
| `API_QUALIFICATIONS_QUOTAS.md` | **Detailed API documentation** |
| `api-specification.json` | **Structured API schema** |
| `SURVEY_API_IMPLEMENTATION.md` | **Code examples & implementation** |
| `SURVEY_API_QUICK_REFERENCE.md` | **This file - quick lookup** |

---

## 8 Survey Qualifications Tracked

1. **Quota Status** - completed / total
2. **Project Name** - survey title
3. **Country Code** - geography
4. **LOI** - duration in minutes
5. **Incidence Rate** - completion % (0-100)
6. **Completion Count** - current count
7. **Reward Amount** - cents or coins
8. **Custom Parameters** - age, gender, job, etc.

---

**Created:** March 2026  
**Version:** 1.0  
**Scope:** Survey API Reference (Qualifications & Quotas)
