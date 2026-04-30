// materials.js
import * as THREE from 'three'

export class BackgroundMaterial extends THREE.RawShaderMaterial {
  constructor() {
    super(BackgroundMaterial.shader)
    this.resize = () => {
      this.uniforms.resolution.value.set(
        window.innerWidth * window.devicePixelRatio,
        window.innerHeight * devicePixelRatio
      )
    }
    this.loop = (timestamp) => {
      requestAnimationFrame(this.loop)
      this.uniforms.globalTime.value = timestamp / 1000
    }
    window.addEventListener('resize', this.resize)
    requestAnimationFrame(this.loop)
  }
}
export class MountainMaterial extends THREE.RawShaderMaterial {
    constructor() {
      super(MountainMaterial.shader)
      this.resize = () => {
        this.uniforms.resolution.value.set(
          window.innerWidth * window.devicePixelRatio,
          window.innerHeight * devicePixelRatio
        )
      }
      this.loop = (timestamp) => {
        requestAnimationFrame(this.loop)
        this.uniforms.globalTime.value = timestamp / 1000
      }
      window.addEventListener('resize', this.resize)
      requestAnimationFrame(this.loop)
    }
  }
  export class MountainMaterial extends THREE.ShaderMaterial {
    constructor() {
      super(MountainMaterial.shader)
    }
  }
  
  MountainMaterial.shader = {
    uniforms: Object.assign({}, THREE.UniformsLib.fog, {
      fogColor: { value: null },
      fogNear: { value: 1 },
      fogFar: { value: 2000 }
    }),
    vertexShader: `
      uniform vec3 mvPosition;
      varying vec2 vUv;
      varying float fogDepth;
      void main() {
        fogDepth = -mvPosition.z;
        vUv = uv;
        gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0);
      }
    `,
    fragmentShader: `
      #ifdef GL_ES
      precision mediump float;
      #endif
      varying vec2 vUv;
      #include <fog_pars_fragment>
      float random(vec2 co) {
          return fract(sin(dot(co.xy, vec2(12.9898,78.233))) * 43758.5453);
      }
      vec2 rand2(vec2 p) {
          p = vec2(dot(p, vec2(12.9898,78.233)), dot(p, vec2(26.65125, 83.054543))); 
          return fract(sin(p) * 43758.5453);
      }
      float rand(vec2 p) {
          return fract(sin(dot(p.xy ,vec2(54.90898,18.233))) * 4337.5453);
      }
      void main() {
        float offset = random(vec2(gl_FragCoord.w));
        vec2 c = vUv;
        vec2 p = vUv;
        p *= .3;
        p.y = p.y * 30. - 4.;
        p.x = p.x * (80. * offset) + 14.8 * offset;
        vec2 q = (p - vec2(0.5,0.5)) * 1.;
        vec3 col = vec3(0.);
        float h = max(
          .0,
          max(
            max(
              abs(fract(p.x)-.5-.25, 
              3.*(abs(fract(.7*p.x+.4)-.5-.4) 
            ),
            max(
              1.2*(abs(fract(.8*p.x+.6)-.5-.2), 
              .3*(abs(fract(.5*p.x+.2)-.5) 
            ) 
          )
        );
        float fill = 1.0 - smoothstep(h, h+.001, p.y);
        vec3 col2 = col * min(fill, 2.0);
        gl_FragColor = vec4(col2, fill);
        #ifdef USE_FOG
          #ifdef USE_LOGDEPTHBUF_EXT
            float depth = gl_FragDepthEXT / gl_FragCoord.w;
          #else
            float depth = gl_FragCoord.z / gl_FragCoord.w;
          #endif
          float fogFactor = smoothstep(fogNear, fogFar, depth);
          gl_FragColor.rgb = mix(gl_FragColor.rgb, fogColor, fogFactor);
        #endif
      }
    `,
    fog: true,
    transparent: true
  }
  
  export class TreeMaterial extends THREE.RawShaderMaterial {
    constructor() {
      super(TreeMaterial.shader)
      this.loop = (timestamp) => {
        requestAnimationFrame(this.loop)
        this.uniforms.globalTime.value = timestamp / 1000
      }
      requestAnimationFrame(this.loop)
    }
  }
  
  TreeMaterial.shader = {
    uniforms: {
      globalTime: { value: performance.now() / 1000 }
    },
    vertexShader: `
      attribute vec3 position;
      attribute vec2 uv;
      uniform mat4 projectionMatrix;
      uniform mat4 modelViewMatrix;
      varying vec2 vUv;
      void main() {
        vUv = uv;
        gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0);
      }
    `,
    fragmentShader: `
      #ifdef GL_ES
      precision mediump float;
      #endif
      #define RGB(r, g, b) vec3(float(r) / 255.0, float(g) / 255.0, float(b) / 255.0)
      uniform float globalTime;
      varying vec2 vUv;
      float treeFill(in float size, in vec2 offset) {
        vec2 p = vUv;
        vec2 q = p - vec2(0.5,0.5);
          vec2 q1 = 100.0 / size * q - offset;
          float r= mod(-0.8*q1.y,1.-0.06*q1.y) * -0.05*q1.y - .1*q1.y;
          float fill = (1.0 - smoothstep(r, r+0.001, abs(q1.x+0.5*sin(0.9 * globalTime + p.x * 25.0)*(1.0 + q1.y/13.0)))) * smoothstep(0.0, 0.01, q1.y + 13.0);
          return fill;
      }
      vec4 tree(in float size, in vec2 offset) {
        float glowDist = 0.12;
        vec3 glowColor = RGB(11, 115, 95);
        float tree = treeFill(size, offset);
        float treeGlow = treeFill(size, vec2(offset.x + glowDist, offset.y));
        return max(vec4(glowColor * (treeGlow - tree), treeGlow), vec4(0.0));
      }
      void main() {
        vec2 c = vUv;
        vec2 p = vUv;
        p *= 0.3;
        p.y = p.y * 30.0 - 4.0;
        p.x = p.x * 30.0;
        vec2 q = (p - vec2(0.5,0.5)) * 5.5;
        vec4 col = tree(1.0, vec2(-30.0, 7.0));
              col += tree(1.2, vec2(-15.0, 8.0));
              col += tree(1.1, vec2(-12.0, 4.0));
              col += tree(1.0, vec2(-9.0, 6.0));
              col += tree(1.1, vec2(-10.0, 3.0));
              col += tree(1.0, vec2(-3.0, 4.0));
              col += tree(1.1, vec2(-1.5, 5.0));
              col += tree(1.0, vec2(5.0, 3.0));
              col += tree(1.3, vec2(12.0, 8.0));
              col += tree(0.9, vec2(15.0, 7.0));
              col += tree(1.0, vec2(18.0, 7.0));
              col += tree(1.1, vec2(26.0, 7.0));
        gl_FragColor = vec4(max(col.rgb * p.y, vec3(0.0)), col.a);
      }
    `,
    transparent: true
  }