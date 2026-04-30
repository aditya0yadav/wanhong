# Survey API Implementation Guide

## Quick Start: Survey Qualifications & Quotas API

This guide shows how to implement the API calls for survey qualifications and quota fetching in your survey platform.

---

## 1. Basic API Calls

### 1.1 Import API Functions

```javascript
// src/views/survey-component.vue
import {
  getPlatformList,
  getFeatured,
  getOffers,
  getQuota
} from '@/api/modules/platform'

import { ref } from 'vue'
```

---

## 2. Load Available Platforms

```javascript
// Get all available survey platforms
const platforms = ref([])

const loadPlatforms = async () => {
  try {
    const response = await getPlatformList()
    platforms.value = response.data
    
    // Filter platforms by type
    const walls = platforms.value.filter(p => p.is_wall === 1)
    const lists = platforms.value.filter(p => p.is_list === 1)
    
    console.log(`Loaded ${platforms.value.length} platforms`)
  } catch (error) {
    console.error('Failed to load platforms:', error)
  }
}
```

---

## 3. Load Featured Surveys

```javascript
// Get trending/featured surveys
const featuredSurveys = ref([])

const loadFeaturedSurveys = async () => {
  try {
    const response = await getFeatured()
    featuredSurveys.value = response.data
    
    // Use for carousel display
    featuredSurveys.value.forEach(survey => {
      console.log(`Featured: ${survey.project_name} - $${survey.project_cpi/100}`)
    })
  } catch (error) {
    console.error('Failed to load featured surveys:', error)
  }
}
```

---

## 4. Load Survey Offers with Qualifications

### 4.1 Basic Load

```javascript
// Get survey offers for specific platform
const surveys = ref([])
const totalCount = ref(0)
const visibilityFlags = ref({})

const loadSurveyOffers = async (platformId) => {
  try {
    const response = await getOffers({
      platform_id: platformId,
      page: 1,
      limit: 20,
      sort_field: 'update_time',
      sort_value: 'desc'
    })
    
    // Extract data
    const { list, count } = response.data
    totalCount.value = count
    
    // Store visibility flags for UI control
    visibilityFlags.value = {
      quota: response.data.show_quota,
      name: response.data.show_name,
      loi: response.data.show_loi,
      ir: response.data.show_ir,
      complete: response.data.show_complete
    }
    
    // Process surveys
    surveys.value = list.map(survey => processSurvey(survey))
    
    console.log(`Loaded ${surveys.value.length} surveys`)
  } catch (error) {
    console.error('Failed to load offers:', error)
    surveys.value = []
  }
}
```

### 4.2 Process Survey Data

```javascript
// Transform API response to usable survey object
const processSurvey = (rawSurvey) => {
  return {
    // Basic Info
    id: rawSurvey.project_pno,
    name: rawSurvey.project_name,
    
    // Qualifications
    qualifications: {
      quota: {
        total: rawSurvey.project_quota,
        completed: rawSurvey.project_complete,
        remaining: rawSurvey.project_quota - rawSurvey.project_complete,
        percentFilled: (rawSurvey.project_complete / rawSurvey.project_quota) * 100
      },
      duration: {
        minutes: rawSurvey.project_loi,
        label: `${rawSurvey.project_loi} min`
      },
      incidence: {
        percentage: rawSurvey.project_ir,
        label: `${rawSurvey.project_ir}%`
      },
      country: rawSurvey.code,
      reward: {
        cents: rawSurvey.project_cpi,
        usd: (rawSurvey.project_cpi / 100).toFixed(2),
        coins: rawSurvey.project_cpi,
        display: `$${(rawSurvey.project_cpi / 100).toFixed(2)}`
      }
    },
    
    // Custom Requirements
    requirements: parseCustomQualifications(rawSurvey.project_params),
    
    // Quota Details
    hasDetailedQuota: rawSurvey.platform.is_quota === 1,
    platform: rawSurvey.platform,
    
    // Timestamps
    createdAt: rawSurvey.create_time,
    updatedAt: rawSurvey.update_time
  }
}

// Parse custom qualification JSON
const parseCustomQualifications = (jsonString) => {
  if (!jsonString) return []
  
  try {
    const params = JSON.parse(jsonString)
    return params.map(param => ({
      name: param.name,
      value: param.value,
      label: formatParameterLabel(param.name, param.value)
    }))
  } catch (error) {
    console.error('Failed to parse qualifications:', error)
    return []
  }
}

// Format parameter display
const formatParameterLabel = (name, value) => {
  const labels = {
    'age_group': 'Age',
    'gender': 'Gender',
    'employment': 'Employment',
    'income_level': 'Income',
    'education': 'Education',
    'occupation': 'Occupation'
  }
  
  const label = labels[name] || name
  return `${label}: ${value}`
}
```

---

## 5. Pagination & Filtering

### 5.1 Pagination

```javascript
const query = ref({
  page: 1,
  limit: 20,
  platform_id: '',
  sort_field: 'update_time',
  sort_value: 'desc'
})

const handlePageChange = (newPage) => {
  query.value.page = newPage
  loadSurveyOffers(query.value.platform_id)
}

const handlePageSizeChange = (newLimit) => {
  query.value.limit = newLimit
  query.value.page = 1
  loadSurveyOffers(query.value.platform_id)
}
```

### 5.2 Search & Filter

```javascript
const searchQuery = ref('')

const handleSearch = () => {
  query.value.page = 1
  query.value.project_name = searchQuery.value
  loadSurveyOffers(query.value.platform_id)
}

const handleCountryFilter = (countryCode) => {
  query.value.page = 1
  query.value.code = countryCode
  loadSurveyOffers(query.value.platform_id)
}

const handleSort = (field, order) => {
  query.value.sort_field = field
  query.value.sort_value = order
  query.value.page = 1
  loadSurveyOffers(query.value.platform_id)
}
```

---

## 6. Load Detailed Quota

### 6.1 Fetch Quota Details

```javascript
const quotaDetails = ref(null)
const quotaLoading = ref(false)
const quotaType = ref('') // 'html' or 'link'

const loadQuotaDetails = async (survey) => {
  // Check if survey has detailed quota
  if (!survey.hasDetailedQuota) {
    console.log('No detailed quota available for this survey')
    return
  }
  
  quotaLoading.value = true
  
  try {
    const response = await getQuota({
      project_pno: survey.id
    })
    
    const { type, content } = response.data
    
    quotaType.value = type
    quotaDetails.value = {
      type: type,
      content: type === 'html' ? content : content,
      survey: survey.name,
      fetchedAt: new Date()
    }
    
    console.log(`Loaded ${type} quota for ${survey.name}`)
  } catch (error) {
    console.error('Failed to load quota details:', error)
    quotaDetails.value = null
  } finally {
    quotaLoading.value = false
  }
}
```

### 6.2 Display Quota

```javascript
// In template:
// <el-dialog v-model="quotaVisible" title="Survey Quota Details">
//   <div v-if="quotaLoading" class="loading">Loading...</div>
//   
//   <!-- HTML Type -->
//   <div v-if="quotaType === 'html' && quotaDetails" 
//        v-html="quotaDetails.content"
//        class="quota-html-content">
//   </div>
//   
//   <!-- Link Type -->
//   <iframe v-if="quotaType === 'link' && quotaDetails"
//           :src="quotaDetails.content"
//           class="quota-iframe">
//   </iframe>
// </el-dialog>
```

---

## 7. Complete Example: Survey List Component

```vue
<template>
  <div class="survey-list">
    <!-- Filters -->
    <div class="filters">
      <input v-model="searchQuery" placeholder="Search surveys...">
      <select v-model="countryFilter" @change="handleCountryFilter">
        <option value="">All Countries</option>
        <option value="US">USA</option>
        <option value="UK">UK</option>
        <option value="CA">Canada</option>
      </select>
      <button @click="handleSearch">Search</button>
    </div>
    
    <!-- Survey Table -->
    <table class="survey-table" v-if="visibilityFlags">
      <thead>
        <tr>
          <th>Survey Name</th>
          <th v-if="visibilityFlags.quota">Quota</th>
          <th v-if="visibilityFlags.loi">Duration</th>
          <th v-if="visibilityFlags.ir">Completion Rate</th>
          <th>Reward</th>
          <th>Requirements</th>
          <th v-if="hasQuota">Quota Details</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="survey in surveys" :key="survey.id">
          <td>{{ survey.name }}</td>
          
          <!-- Quota Column -->
          <td v-if="visibilityFlags.quota" class="quota-cell">
            {{ survey.qualifications.quota.completed }} / 
            {{ survey.qualifications.quota.total }}
            <div class="progress-bar">
              <div class="progress" 
                   :style="{ width: survey.qualifications.quota.percentFilled + '%' }">
              </div>
            </div>
          </td>
          
          <!-- Duration Column -->
          <td v-if="visibilityFlags.loi">
            {{ survey.qualifications.duration.label }}
          </td>
          
          <!-- Incidence Rate Column -->
          <td v-if="visibilityFlags.ir">
            {{ survey.qualifications.incidence.label }}
          </td>
          
          <!-- Reward -->
          <td class="reward">
            <strong>${{ survey.qualifications.reward.display }}</strong>
          </td>
          
          <!-- Requirements -->
          <td class="requirements">
            <span v-for="req in survey.requirements" 
                  :key="req.name" 
                  class="req-tag">
              {{ req.label }}
            </span>
          </td>
          
          <!-- Quota Details Button -->
          <td v-if="hasQuota">
            <button v-if="survey.hasDetailedQuota" 
                    @click="showQuotaModal(survey)"
                    class="quota-btn">
              View
            </button>
          </td>
          
          <!-- Actions -->
          <td class="actions">
            <button @click="startSurvey(survey)" class="start-btn">
              Start Survey
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    
    <!-- Pagination -->
    <div class="pagination">
      <button @click="handlePageChange(query.page - 1)" 
              :disabled="query.page === 1">
        Previous
      </button>
      <span>Page {{ query.page }} of {{ Math.ceil(totalCount / query.limit) }}</span>
      <button @click="handlePageChange(query.page + 1)"
              :disabled="query.page * query.limit >= totalCount">
        Next
      </button>
    </div>
    
    <!-- Quota Modal -->
    <el-dialog title="Survey Quota Details" v-model="quotaVisible">
      <div v-if="quotaLoading" class="loading">Loading quota details...</div>
      
      <div v-if="quotaType === 'html' && quotaDetails"
           v-html="quotaDetails.content"
           class="quota-content">
      </div>
      
      <iframe v-if="quotaType === 'link' && quotaDetails"
              :src="quotaDetails.content"
              class="quota-iframe"
              style="width: 100%; height: 600px;">
      </iframe>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getPlatformList, getFeatured, getOffers, getQuota } from '@/api/modules/platform'

// State
const surveys = ref([])
const totalCount = ref(0)
const visibilityFlags = ref({})
const quotaVisible = ref(false)
const quotaLoading = ref(false)
const quotaType = ref('')
const quotaDetails = ref(null)
const searchQuery = ref('')
const countryFilter = ref('')
const platformId = ref('1') // From route params

const query = ref({
  page: 1,
  limit: 20,
  platform_id: platformId.value,
  sort_field: 'update_time',
  sort_value: 'desc'
})

// Methods
const loadSurveyOffers = async () => {
  try {
    const response = await getOffers(query.value)
    surveys.value = response.data.list.map(processSurvey)
    totalCount.value = response.data.count
    
    visibilityFlags.value = {
      quota: response.data.show_quota,
      name: response.data.show_name,
      loi: response.data.show_loi,
      ir: response.data.show_ir,
      complete: response.data.show_complete
    }
  } catch (error) {
    console.error('Failed to load surveys:', error)
  }
}

const processSurvey = (raw) => {
  return {
    id: raw.project_pno,
    name: raw.project_name,
    qualifications: {
      quota: {
        total: raw.project_quota,
        completed: raw.project_complete,
        remaining: raw.project_quota - raw.project_complete,
        percentFilled: (raw.project_complete / raw.project_quota) * 100
      },
      duration: { label: `${raw.project_loi} min` },
      incidence: { label: `${raw.project_ir}%` },
      reward: { display: (raw.project_cpi / 100).toFixed(2) }
    },
    requirements: parseQualifications(raw.project_params),
    hasDetailedQuota: raw.platform.is_quota === 1
  }
}

const parseQualifications = (jsonStr) => {
  if (!jsonStr) return []
  try {
    return JSON.parse(jsonStr)
  } catch (e) {
    return []
  }
}

const handleSearch = () => {
  query.value.page = 1
  query.value.project_name = searchQuery.value
  loadSurveyOffers()
}

const handleCountryFilter = () => {
  query.value.page = 1
  query.value.code = countryFilter.value
  loadSurveyOffers()
}

const handlePageChange = (page) => {
  query.value.page = page
  loadSurveyOffers()
}

const showQuotaModal = async (survey) => {
  quotaLoading.value = true
  quotaVisible.value = true
  
  try {
    const response = await getQuota({ project_pno: survey.id })
    quotaType.value = response.data.type
    quotaDetails.value = response.data
  } catch (error) {
    console.error('Failed to load quota:', error)
  } finally {
    quotaLoading.value = false
  }
}

const startSurvey = (survey) => {
  console.log(`Starting survey: ${survey.name}`)
  // Navigate to survey or open in new window
}

// Lifecycle
onMounted(() => {
  loadSurveyOffers()
})
</script>

<style scoped>
.survey-table {
  width: 100%;
  border-collapse: collapse;
}

.survey-table th, .survey-table td {
  padding: 12px;
  border: 1px solid #ddd;
  text-align: left;
}

.survey-table th {
  background: #f5f5f5;
  font-weight: bold;
}

.quota-cell {
  min-width: 150px;
}

.progress-bar {
  width: 100%;
  height: 20px;
  background: #f0f0f0;
  border-radius: 10px;
  overflow: hidden;
  margin-top: 4px;
}

.progress {
  height: 100%;
  background: #4CAF50;
  transition: width 0.3s;
}

.reward {
  color: #2196F3;
  font-weight: bold;
}

.req-tag {
  display: inline-block;
  padding: 4px 8px;
  background: #e3f2fd;
  border-radius: 4px;
  font-size: 12px;
  margin-right: 4px;
}
</style>
```

---

## 8. API Implementation Checklist

- [ ] Import all API functions
- [ ] Load platforms on component mount
- [ ] Load featured surveys
- [ ] Implement survey list loading
- [ ] Parse custom qualifications JSON
- [ ] Apply visibility flags to table columns
- [ ] Implement pagination
- [ ] Add search functionality
- [ ] Add country filter
- [ ] Implement quota details modal
- [ ] Handle loading states
- [ ] Handle error states
- [ ] Test with different data scenarios

---

## 9. Common Data Transformations

### Calculate Quota Remaining
```javascript
const remaining = survey.quota.total - survey.quota.completed;
const percentFilled = (survey.quota.completed / survey.quota.total) * 100;
```

### Format Currency
```javascript
const usdAmount = survey.reward.cents / 100;
const formatted = `$${usdAmount.toFixed(2)}`;
```

### Parse Custom Requirements
```javascript
const requirements = JSON.parse(survey.project_params);
const requirementsList = requirements
  .map(r => `${r.name}: ${r.value}`)
  .join(', ');
```

---

## 10. Debugging Tips

```javascript
// Log API response
console.log('Full response:', response.data)

// Check visibility flags
console.log('Show quota?', visibilityFlags.quota)

// Validate JSON parsing
try {
  const params = JSON.parse(survey.project_params)
  console.log('Parsed params:', params)
} catch (e) {
  console.error('Invalid JSON:', e)
}

// Verify quota calculation
console.log(`Quota: ${completed}/${total} = ${percentFilled.toFixed(2)}%`)
```

---

**Last Updated:** March 2026  
**API Version:** 1.0  
**Focus:** Survey Qualifications & Quotas Implementation
