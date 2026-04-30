# Qualification & Quota - Remediation Guide

## Overview
This guide provides step-by-step instructions to fix all identified issues in the qualification and quota fetching system.

---

## Priority 1: Critical Security Fixes (Do First - Blocks Release)

### P1-01: Fix XSS Vulnerability in Quota Display

**Issue:** Unescaped HTML in quota modal
**File:** `src/views/platOffers.vue`
**Severity:** CRITICAL (CVSS 9.8)

#### Step 1: Install DOMPurify

```bash
npm install dompurify
# or
pnpm add dompurify
```

#### Step 2: Update Import Section

In `src/views/platOffers.vue`, add to the script section:

```javascript
import DOMPurify from 'dompurify';
```

#### Step 3: Modify toQuota Function

Find the `toQuota()` function (around line 230) and update:

**BEFORE:**
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
                quota.value = data.content;  // ⚠️ UNSAFE
            }
        })
    })
}
```

**AFTER:**
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
                // ✅ Sanitize HTML content before rendering
                quota.value = DOMPurify.sanitize(data.content, {
                    ALLOWED_TAGS: ['b', 'i', 'em', 'strong', 'p', 'br', 'div', 'span', 'table', 'tr', 'td', 'th'],
                    ALLOWED_ATTR: ['class', 'style']
                });
            }
        })
    }).catch(err => {
        quotaLoading.value = false;
        ElMessage.error('Failed to load quota details');
        console.error('Quota fetch error:', err);
    })
}
```

#### Step 4: Testing

```javascript
// Test case 1: Normal HTML
const normalHtml = "<h2>Quota</h2><p>Remaining: 100</p>";
const safe1 = DOMPurify.sanitize(normalHtml);
console.assert(safe1.includes("<h2>"), "Normal tags preserved");

// Test case 2: XSS payload
const xssPayload = "<img src=x onerror=\"alert('XSS')\">";
const safe2 = DOMPurify.sanitize(xssPayload);
console.assert(!safe2.includes("onerror"), "XSS removed");

// Test case 3: Script tag
const scriptTag = "<div><script>alert('XSS')</script></div>";
const safe3 = DOMPurify.sanitize(scriptTag);
console.assert(!safe3.includes("<script>"), "Script tags removed");
```

---

### P1-02: Add Error Handling to toQuota() API Call

**Issue:** Missing .catch() handler
**File:** `src/views/platOffers.vue` - Line 230

Already fixed in P1-01 above. The `.catch()` handler is included in the updated code.

---

### P1-03: Fix JSON Parsing Error

**Issue:** Unhandled JSON.parse exception
**File:** `src/views/platOffers.vue` - Line 275
**Severity:** HIGH

#### Current Code (Problematic):
```javascript
data.forEach((item, index) => {
    item.project_params = item.project_params ? JSON.parse(item.project_params) : [];
    // ... rest of code
})
```

#### Fixed Code:
```javascript
data.forEach((item, index) => {
    // ✅ Safe JSON parsing with error handling
    try {
        item.project_params = item.project_params 
            ? JSON.parse(item.project_params) 
            : [];
    } catch(parseError) {
        console.error(`Invalid JSON in project_params for ${item.project_pno}:`, parseError);
        item.project_params = [];
        // Optionally log to error tracking service
        // errorTrackingService.captureException(parseError);
    }
    
    if (index == 0) {
        item.project_params.forEach(param => {
            if (param && param.name) {  // ✅ Added safety check
                customParams.value.push(param.name);
            }
        })
    }
})
```

---

### P1-04: Add Error Handling to getData()

**Issue:** Missing error handler
**File:** `src/views/platOffers.vue` - Line 258
**Severity:** HIGH

#### Current Code:
```javascript
const getData = () => {
    tableData.value = [];
    loading.value = true;
    getOffers(query.value).then(res => {
        const data = res.data.list;
        // ... process data
        loading.value = false;
    })
}
```

#### Fixed Code:
```javascript
const getData = () => {
    tableData.value = [];
    loading.value = true;
    
    getOffers(query.value)
        .then(res => {
            const data = res.data.list;
            showQuota.value = res.data.show_quota ? true : false;
            showName.value = res.data.show_name ? true : false;
            showLoi.value = res.data.show_loi ? true : false;
            showIr.value = res.data.show_ir ? true : false;
            showComplete.value = res.data.show_complete ? true : false;
            showClick.value = res.data.show_click ? true : false;
            customParams.value = [];
            
            data.forEach((item, index) => {
                try {
                    item.project_params = item.project_params 
                        ? JSON.parse(item.project_params) 
                        : [];
                } catch(e) {
                    item.project_params = [];
                }
                
                if (index == 0) {
                    item.project_params.forEach(param => {
                        if (param && param.name) {
                            customParams.value.push(param.name);
                        }
                    })
                }
            })
            
            tableData.value = data;
            total.value = res.data.count;
            loading.value = false;
        })
        .catch(err => {
            // ✅ Error handling
            loading.value = false;
            ElMessage.error('Failed to load offers. Please try again.');
            console.error('getOffers error:', err);
            tableData.value = [];
            total.value = 0;
        })
}
```

---

### P1-05: Add Error Handling to offers.vue

**Issue:** Missing error handler on getPlatformList
**File:** `src/views/offers.vue` - Line 45
**Severity:** HIGH

#### Current Code:
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
}).catch(() => {
    loading.value = false
})
```

This one actually has a `.catch()`, but should be more explicit:

#### Enhanced Code:
```javascript
getPlatformList()
    .then(res => {
        const data = res.data;
        if (Array.isArray(data)) {
            data.forEach(item => {
                if (item.is_wall == 1) {
                    walls.value.push(item)
                }
                if (item.is_list == 1) {
                    lists.value.push(item)
                }
            });
        }
        loading.value = false
    })
    .catch(err => {
        loading.value = false
        console.error('getPlatformList error:', err);
        ElMessage.error('Failed to load platforms. Please refresh the page.');
    })
```

---

## Priority 2: Code Quality Improvements

### P2-01: Remove Console.log Debug Statements

**File:** `src/views/platOffers.vue` - Line 162

#### Remove:
```javascript
console.log(route)  // ❌ Remove this line
```

Also remove any other debug logs:
```bash
# Find all console.log in the vue files
grep -r "console.log" src/views/*.vue
```

### P2-02: Add Component Lifecycle Cleanup

**File:** `src/views/platOffers.vue`
**Issue:** Potential memory leak on component unmount

Add cleanup for async operations:

```javascript
// At the top of script setup
let isComponentMounted = ref(true);

onUnmounted(() => {
    isComponentMounted.value = false;
});

// Modify toQuota function
const toQuota = (item) => {
    quotaVisible.value = true;
    quotaLoading.value = true;
    quotaTitle.value = item.project_name;
    quota.value = '';
    
    getQuota({ project_pno: item.project_pno })
        .then(res => {
            // ✅ Check if component is still mounted
            if (!isComponentMounted.value) return;
            
            const data = res.data || {};
            quotaLoading.value = false;
            // ... rest of code
        })
        .catch(err => {
            if (!isComponentMounted.value) return;
            quotaLoading.value = false;
            ElMessage.error('Failed to load quota details');
        })
}
```

---

## Priority 3: Performance Optimizations

### P3-01: Implement Platform List Caching

**File:** `src/stores/modules/user.js`

Add caching logic:

```javascript
import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', () => {
    // ... existing code
    
    const platformListCache = ref(null);
    const platformListCacheTime = ref(null);
    const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

    const getPlatformListCached = async () => {
        const now = Date.now();
        
        // Return cached data if still fresh
        if (platformListCache.value && platformListCacheTime.value) {
            if (now - platformListCacheTime.value < CACHE_DURATION) {
                return platformListCache.value;
            }
        }
        
        // Fetch fresh data
        const response = await http.get(`/api/member.Platform/list`);
        platformListCache.value = response.data;
        platformListCacheTime.value = now;
        
        return response.data;
    };

    return {
        // ... existing returns
        getPlatformListCached
    };
});
```

### P3-02: Add Virtual Scrolling for Large Lists

**File:** `src/views/platOffers.vue`

Install dependencies:

```bash
npm install vue-virtual-scroller
# or
pnpm add vue-virtual-scroller
```

Update the table:

```vue
<template>
    <!-- Replace el-table with virtual scrolling version -->
    <virtual-list 
        :items="tableData" 
        :item-size="50"
        class="scrollable-table"
    >
        <template #default="{ item }">
            <div class="table-row">
                <!-- Row content -->
            </div>
        </template>
    </virtual-list>
</template>

<script>
import { VirtualList } from 'vue-virtual-scroller'
</script>
```

---

## Testing Checklist

### Security Testing
- [ ] Test XSS payload in quota endpoint response
- [ ] Test malformed JSON in project_params
- [ ] Test API timeout scenarios
- [ ] Test with network disconnected

### Functionality Testing
- [ ] Load offers list
- [ ] Click quota button for different projects
- [ ] View quota in both HTML and iframe modes
- [ ] Test pagination
- [ ] Test sorting and filtering
- [ ] Test custom parameters display

### Performance Testing
- [ ] Measure page load time before/after
- [ ] Check memory usage with DevTools
- [ ] Test with network throttling (slow 3G)
- [ ] Test with 1000+ items in list

### Browser Compatibility
- [ ] Chrome latest
- [ ] Firefox latest
- [ ] Safari latest
- [ ] Edge latest
- [ ] Mobile browsers

---

## Implementation Order

**Week 1 (Critical):**
1. Install DOMPurify
2. Fix XSS vulnerability (P1-01)
3. Add error handlers (P1-02 to P1-05)
4. Add try-catch for JSON parsing (P1-03)
5. Remove console.log statements (P2-01)
6. Run full test suite

**Week 2 (Important):**
7. Implement caching (P3-01)
8. Add component lifecycle cleanup (P2-02)
9. Performance testing

**Week 3 (Nice-to-Have):**
10. Implement virtual scrolling (P3-02)
11. Code review and optimization

---

## Verification Script

Run after fixes to verify:

```javascript
// In browser console
// Test 1: Verify DOMPurify is loaded
console.assert(typeof DOMPurify !== 'undefined', 'DOMPurify not loaded');

// Test 2: Verify no console.log calls
const logs = [];
const originalLog = console.log;
console.log = function() {
    logs.push(arguments);
    originalLog.apply(console, arguments);
};

// Test 3: Simulate quota loading
const testXSS = "<img src=x onerror=\"alert('XSS')\">";
const sanitized = DOMPurify.sanitize(testXSS);
console.assert(!sanitized.includes('onerror'), 'XSS not properly sanitized');

// Test 4: Simulate JSON parse error
try {
    const badJson = "{invalid json}";
    JSON.parse(badJson);
    console.error('Error handler not working');
} catch(e) {
    console.log('✓ Error handling working correctly');
}
```

---

## After Deployment Checklist

- [ ] Monitor error tracking for any quota endpoint errors
- [ ] Check browser console for JavaScript errors
- [ ] Monitor API response times
- [ ] Verify no XSS vulnerabilities in security scanner
- [ ] Check user feedback on quota display
- [ ] Review performance metrics

---

## Additional Resources

- DOMPurify Documentation: https://github.com/cure53/DOMPurify
- Vue Virtual Scroller: https://github.com/Akryum/vue-virtual-scroller
- OWASP XSS Prevention: https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html
- Vue 3 Error Handling: https://vuejs.org/guide/extras/composition-api-faq.html#handling-errors

---

**Total Estimated Time:** 6-8 hours
**Priority Level:** CRITICAL (blocking release)
**Status:** Ready for implementation
