# Références de câbles (GraceTHD)

Fichier de référence pour les types de câbles utilisés dans les projets fibre optique.
Ces codes sont stockés dans `t_cable.cb_rf_code` et référencent la table `t_reference`.

## Structure d'un code RF

Format: `RF000000000XXX`

Champs extraits de la description:
- **Fabricant**: ACOME, NEXANS, PRYSMIAN, STERLITE, TELDOR, LS CABLE SYSTEM, GENERIQUE
- **Capacité**: 6FO, 12FO, 24FO, 36FO, 48FO, 72FO, 96FO, 108FO, 144FO, 288FO, 432FO, 576FO, 720FO
- **Modulo**: mod 6 ou mod 12
- **Type d'installation**: 
  - `sout` = souterrain (underground)
  - `aerien` / `aérien` = aerial
  - `mixte` = mixed (aérien + souterrain)
  - `imb` = imbrigué (embedded, typically aerial)

## Règles de calcul

Pour chaque câble, la longueur ajustée (utilisée pour le calcul des matériaux) est:

```
longueur_ajustee = (longueur_cartographique × 1.20) + 10
```

Où:
- `longueur_cartographique` = `cb_lgreel` (longueur réelle déclarée du câble en mètres)
- `× 1.20` = majoration de 20% pour la différence entre longueur cartographique et longueur réelle
- `+ 10` = 5 mètres de lovage (coiling slack) de chaque côté du câble

## Liste des références

### ACOME (OR000000000101)
| Code | FO | Modulo | Installation | Label |
|------|----|--------|-------------|-------|
| RF000000000029 | 144 | mod 12 | sout | AFFERMAGE - câble 144 FO modulo 12 - sout |
| RF000000000030 | 12 | mod 6 | sout | AFFERMAGE - câble 12 FO modulo 6 - sout |
| RF000000000031 | 72 | mod 12 | sout | AFFERMAGE - câble 72 FO modulo 12 - sout |
| RF000000000032 | 12 | mod 6 | mixte | AFFERMAGE - câble 12 FO modulo 6 - mixte |
| RF000000000033 | 12 | mod 6 | imb | AFFERMAGE - câble 12 FO modulo 6 - imb |
| RF000000000034 | 144 | mod 12 | imb | AFFERMAGE - câble 144 FO modulo 12 - imb |
| RF000000000035 | 144 | mod 6 | imb | AFFERMAGE - câble 144 FO modulo 6 - imb |
| RF000000000036 | 144 | mod 6 | mixte | AFFERMAGE - câble 144 FO modulo 6 - mixte |
| RF000000000037 | 144 | mod 6 | sout | AFFERMAGE - câble 144 FO modulo 6 - sout |
| RF000000000038 | 24 | mod 6 | imb | AFFERMAGE - câble 24 FO modulo 6 - imb |
| RF000000000039 | 24 | mod 6 | mixte | AFFERMAGE - câble 24 FO modulo 6 - mixte |
| RF000000000040 | 24 | mod 6 | sout | AFFERMAGE - câble 24 FO modulo 6 - sout |
| RF000000000041 | 288 | mod 12 | imb | AFFERMAGE - câble 288 FO modulo 12 - imb |
| RF000000000042 | 288 | mod 12 | sout | AFFERMAGE - câble 288 FO modulo 12 - sout |
| RF000000000043 | 432 | mod 12 | imb | AFFERMAGE - câble 432 FO modulo 12 - imb |
| RF000000000044 | 432 | mod 12 | sout | AFFERMAGE - câble 432 FO modulo 12 - sout |
| RF000000000045 | 48 | mod 6 | imb | AFFERMAGE - câble 48 FO modulo 6 - imb |
| RF000000000046 | 48 | mod 6 | mixte | AFFERMAGE - câble 48 FO modulo 6 - mixte |
| RF000000000047 | 48 | mod 6 | sout | AFFERMAGE - câble 48 FO modulo 6 - sout |
| RF000000000048 | 576 | mod 12 | sout | AFFERMAGE - câble 576 FO modulo 12 - sout |
| RF000000000049 | 72 | mod 6 | imb | AFFERMAGE - câble 72 FO modulo 6 - imb |
| RF000000000050 | 72 | mod 6 | mixte | AFFERMAGE - câble 72 FO modulo 6 - mixte |
| RF000000000051 | 72 | mod 6 | sout | AFFERMAGE - câble 72 FO modulo 6 - sout |
| RF000000000052 | 720 | mod 12 | sout | AFFERMAGE - câble 720 FO modulo 12 - sout |
| RF000000000053 | 144 | mod 12 | mixte | AFFERMAGE - câble 144 FO modulo 12 - mixte |
| RF000000000054 | 72 | mod 12 | mixte | AFFERMAGE - câble 72 FO modulo 12 - mixte |
| RF000000000055 | 36 | mod 6 | mixte | AFFERMAGE - câble 36 FO modulo 6 - mixte |

### ACOME G657A2 (OR000000000106)
| Code | FO | Modulo | Installation | Label |
|------|----|--------|-------------|-------|
| RF000000000056 | 288 | mod 12 | sout | CABLE_288FO_SOUT_M12_G657A2 |
| RF000000000057 | 144 | mod 12 | mixte | CABLE_144FO_AERO-SOUT_M12_G657A2 |
| RF000000000058 | 72 | mod 12 | mixte | CABLE_72FO_AERO-SOUT_M12_G657A2 |
| RF000000000059 | 144 | mod 6 | mixte | CABLE_144FO_AERO-SOUT_M6_G657A2 |
| RF000000000060 | 12 | mod 6 | mixte | CABLE_12FO_AERO-SOUT_M6_G657A2 |
| RF000000000061 | 36 | mod 6 | mixte | CABLE_36FO_AERO-SOUT_M6_G657A2 |
| RF000000000062 | 72 | mod 6 | mixte | CABLE_72FO_AERO-SOUT_M6_G657A2 |
| RF000000000063 | 432 | mod 12 | sout | CABLE_432FO_SOUT_M12_G657A2 |

### NEXANS (OR000000000111)
| Code | FO | Modulo | Installation | Label |
|------|----|--------|-------------|-------|
| RF000000000064 | 144 | mod 12 | sout | AFFERMAGE - câble 144 FO modulo 12 - sout |
| RF000000000065 | 12 | mod 6 | sout | AFFERMAGE - câble 12 FO modulo 6 - sout |
| RF000000000066 | 72 | mod 12 | sout | AFFERMAGE - câble 72 FO modulo 12 - sout |
| RF000000000067 | 12 | mod 6 | mixte | AFFERMAGE - câble 12 FO modulo 6 - mixte |
| RF000000000068 | 12 | mod 6 | imb | AFFERMAGE - câble 12 FO modulo 6 - imb |
| RF000000000069 | 144 | mod 12 | imb | AFFERMAGE - câble 144 FO modulo 12 - imb |
| RF000000000070 | 144 | mod 6 | imb | AFFERMAGE - câble 144 FO modulo 6 - imb |
| RF000000000071 | 144 | mod 6 | mixte | AFFERMAGE - câble 144 FO modulo 6 - mixte |
| RF000000000072 | 144 | mod 6 | sout | AFFERMAGE - câble 144 FO modulo 6 - sout |
| RF000000000073 | 24 | mod 6 | imb | AFFERMAGE - câble 24 FO modulo 6 - imb |
| RF000000000074 | 24 | mod 6 | mixte | AFFERMAGE - câble 24 FO modulo 6 - mixte |
| RF000000000075 | 24 | mod 6 | sout | AFFERMAGE - câble 24 FO modulo 6 - sout |
| RF000000000076 | 288 | mod 12 | imb | AFFERMAGE - câble 288 FO modulo 12 - imb |
| RF000000000077 | 288 | mod 12 | sout | AFFERMAGE - câble 288 FO modulo 12 - sout |
| RF000000000078 | 432 | mod 12 | imb | AFFERMAGE - câble 432 FO modulo 12 - imb |
| RF000000000079 | 432 | mod 12 | sout | AFFERMAGE - câble 432 FO modulo 12 - sout |
| RF000000000080 | 48 | mod 6 | imb | AFFERMAGE - câble 48 FO modulo 6 - imb |
| RF000000000081 | 48 | mod 6 | mixte | AFFERMAGE - câble 48 FO modulo 6 - mixte |
| RF000000000082 | 48 | mod 6 | sout | AFFERMAGE - câble 48 FO modulo 6 - sout |
| RF000000000083 | 576 | mod 12 | sout | AFFERMAGE - câble 576 FO modulo 12 - sout |
| RF000000000084 | 72 | mod 6 | imb | AFFERMAGE - câble 72 FO modulo 6 - imb |
| RF000000000085 | 72 | mod 6 | mixte | AFFERMAGE - câble 72 FO modulo 6 - mixte |
| RF000000000086 | 72 | mod 6 | sout | AFFERMAGE - câble 72 FO modulo 6 - sout |
| RF000000000087 | 720 | mod 12 | sout | AFFERMAGE - câble 720 FO modulo 12 - sout |
| RF000000000088 | 144 | mod 12 | mixte | AFFERMAGE - câble 144 FO modulo 12 - mixte |
| RF000000000089 | 72 | mod 12 | mixte | AFFERMAGE - câble 72 FO modulo 12 - mixte |
| RF000000000090 | 36 | mod 6 | mixte | AFFERMAGE - câble 36 FO modulo 6 - mixte |

### PRYSMIAN (OR000000000116)
| Code | FO | Modulo | Installation | Label |
|------|----|--------|-------------|-------|
| RF000000000091 | 288 | mod 12 | sout | PRYSMIAN TC00548 |
| RF000000000092 | 12 | mod 6 | sout | PRYSMIAN TC00550 |
| RF000000000093 | 24 | mod 6 | sout | PRYSMIAN TC00550 |
| RF000000000094 | 36 | mod 6 | sout | PRYSMIAN TC00550 |
| RF000000000095 | 48 | mod 6 | sout | PRYSMIAN TC00550 |
| RF000000000096 | 72 | mod 6 | sout | PRYSMIAN TC00550 |
| RF000000000097 | 144 | mod 6 | sout | PRYSMIAN TC00550 |
| RF000000000238 | 6 | mod 6 | mixte | |
| RF000000000239 | 12 | mod 6 | imb | |
| RF000000000240 | 12 | mod 6 | mixte | |
| RF000000000241 | 12 | mod 6 | sout | |
| RF000000000242 | 24 | mod 6 | imb | |
| RF000000000243 | 24 | mod 6 | mixte | |
| RF000000000244 | 24 | mod 6 | sout | |
| RF000000000245 | 36 | mod 12 | mixte | |
| RF000000000246 | 36 | mod 6 | mixte | |
| RF000000000247 | 48 | mod 12 | mixte | |
| RF000000000248 | 48 | mod 6 | imb | |
| RF000000000249 | 48 | mod 6 | mixte | |
| RF000000000250 | 48 | mod 6 | sout | |
| RF000000000251 | 72 | mod 12 | mixte | |
| RF000000000252 | 72 | mod 12 | sout | |
| RF000000000253 | 72 | mod 6 | imb | |
| RF000000000254 | 72 | mod 6 | mixte | |
| RF000000000255 | 72 | mod 6 | sout | |
| RF000000000256 | 96 | mod 12 | mixte | |
| RF000000000257 | 96 | mod 6 | mixte | |
| RF000000000258 | 108 | mod 12 | mixte | |
| RF000000000259 | 108 | mod 6 | mixte | |
| RF000000000260 | 144 | mod 12 | imb | |
| RF000000000261 | 144 | mod 12 | mixte | |
| RF000000000262 | 144 | mod 12 | sout | |
| RF000000000263 | 144 | mod 6 | imb | |
| RF000000000264 | 144 | mod 6 | mixte | |
| RF000000000265 | 144 | mod 6 | sout | |
| RF000000000266 | 288 | mod 12 | imb | |
| RF000000000267 | 288 | mod 12 | sout | |
| RF000000000268 | 432 | mod 12 | imb | |
| RF000000000269 | 432 | mod 12 | sout | |
| RF000000000270 | 576 | mod 12 | sout | |
| RF000000000271 | 720 | mod 12 | sout | |
| RF000000000276 | 288 | mod 12 | sout | TF103G |
| RF000000000277 | 144 | mod 6 | sout | TF103D |
| RF000000000278 | 72 | mod 6 | sout | TF103D |
| RF000000000279 | 48 | mod 6 | sout | TF103D |
| RF000000000280 | 12 | mod 6 | mixte | TF303D |
| RF000000000281 | 48 | mod 6 | mixte | TF303D |
| RF000000000282 | 72 | mod 6 | mixte | TF303D |
| RF000000000283 | 144 | mod 6 | mixte | TF303D |

### STERLITE (OR000000000118)
| Code | FO | Modulo | Installation | Label |
|------|----|--------|-------------|-------|
| RF000000000098 | 144 | mod 12 | sout | |
| RF000000000099 | 12 | mod 6 | sout | |
| RF000000000100 | 72 | mod 12 | sout | |
| RF000000000101 | 12 | mod 6 | mixte | |
| RF000000000102 | 12 | mod 6 | imb | |
| RF000000000103 | 144 | mod 12 | imb | |
| RF000000000104 | 144 | mod 6 | imb | |
| RF000000000105 | 144 | mod 6 | mixte | |
| RF000000000106 | 144 | mod 6 | sout | |
| RF000000000107 | 24 | mod 6 | imb | |
| RF000000000108 | 24 | mod 6 | mixte | |
| RF000000000109 | 24 | mod 6 | sout | |
| RF000000000110 | 288 | mod 12 | imb | |
| RF000000000111 | 288 | mod 12 | sout | |
| RF000000000112 | 432 | mod 12 | imb | |
| RF000000000113 | 432 | mod 12 | sout | |
| RF000000000114 | 48 | mod 6 | imb | |
| RF000000000115 | 48 | mod 6 | mixte | |
| RF000000000116 | 48 | mod 6 | sout | |
| RF000000000117 | 576 | mod 12 | sout | |
| RF000000000118 | 72 | mod 6 | imb | |
| RF000000000119 | 72 | mod 6 | mixte | |
| RF000000000120 | 72 | mod 6 | sout | |
| RF000000000121 | 720 | mod 12 | sout | |
| RF000000000122 | 144 | mod 12 | mixte | |
| RF000000000123 | 72 | mod 12 | mixte | |
| RF000000000124 | 36 | mod 6 | mixte | |

### TELDOR (OR000000000122)
| Code | FO | Modulo | Installation | Label |
|------|----|--------|-------------|-------|
| RF000000000161 | 144 | mod 12 | sout | |
| RF000000000162 | 12 | mod 6 | sout | |
| RF000000000163 | 72 | mod 12 | sout | |
| RF000000000164 | 12 | mod 6 | mixte | |
| RF000000000165 | 12 | mod 6 | imb | |
| RF000000000166 | 144 | mod 12 | imb | |
| RF000000000167 | 144 | mod 6 | imb | |
| RF000000000168 | 144 | mod 6 | mixte | |
| RF000000000169 | 144 | mod 6 | sout | |
| RF000000000170 | 24 | mod 6 | imb | |
| RF000000000171 | 24 | mod 6 | mixte | |
| RF000000000172 | 24 | mod 6 | sout | |
| RF000000000173 | 288 | mod 12 | imb | |
| RF000000000174 | 288 | mod 12 | sout | |
| RF000000000175 | 432 | mod 12 | imb | |
| RF000000000176 | 432 | mod 12 | sout | |
| RF000000000177 | 48 | mod 6 | imb | |
| RF000000000178 | 48 | mod 6 | mixte | |
| RF000000000179 | 48 | mod 6 | sout | |
| RF000000000180 | 576 | mod 12 | sout | |
| RF000000000181 | 72 | mod 6 | imb | |
| RF000000000182 | 72 | mod 6 | mixte | |
| RF000000000183 | 72 | mod 6 | sout | |
| RF000000000184 | 720 | mod 12 | sout | |
| RF000000000185 | 144 | mod 12 | mixte | |
| RF000000000186 | 72 | mod 12 | mixte | |
| RF000000000187 | 36 | mod 6 | mixte | |

### LS CABLE SYSTEM (OR000000000121)
| Code | FO | Modulo | Installation | Label |
|------|----|--------|-------------|-------|
| RF000000000134 | 144 | mod 12 | sout | |
| RF000000000135 | 12 | mod 6 | sout | |
| RF000000000136 | 72 | mod 12 | sout | |
| RF000000000137 | 12 | mod 6 | mixte | |
| RF000000000138 | 12 | mod 6 | imb | |
| RF000000000139 | 144 | mod 12 | imb | |
| RF000000000140 | 144 | mod 6 | imb | |
| RF000000000141 | 144 | mod 6 | mixte | |
| RF000000000142 | 144 | mod 6 | sout | |
| RF000000000143 | 24 | mod 6 | imb | |
| RF000000000144 | 24 | mod 6 | mixte | |
| RF000000000145 | 24 | mod 6 | sout | |
| RF000000000146 | 288 | mod 12 | imb | |
| RF000000000147 | 288 | mod 12 | sout | |
| RF000000000148 | 432 | mod 12 | imb | |
| RF000000000149 | 432 | mod 12 | sout | |
| RF000000000150 | 48 | mod 6 | imb | |
| RF000000000151 | 48 | mod 6 | mixte | |
| RF000000000152 | 48 | mod 6 | sout | |
| RF000000000153 | 576 | mod 12 | sout | |
| RF000000000154 | 72 | mod 6 | imb | |
| RF000000000155 | 72 | mod 6 | mixte | |
| RF000000000156 | 72 | mod 6 | sout | |
| RF000000000157 | 720 | mod 12 | sout | |
| RF000000000158 | 144 | mod 12 | mixte | |
| RF000000000159 | 72 | mod 12 | mixte | |
| RF000000000160 | 36 | mod 6 | mixte | |

### GENERIQUE (OR000000000120)
| Code | FO | Modulo | Installation | Label |
|------|----|--------|-------------|-------|
| RF000000000125 | 12 | mod 6 | mixte | |
| RF000000000126 | 144 | mod 12 | mixte | |
| RF000000000127 | 72 | mod 12 | mixte | |
| RF000000000128 | 144 | mod 6 | mixte | |
| RF000000000129 | 36 | mod 6 | mixte | |
| RF000000000130 | 72 | mod 6 | mixte | |
| RF000000000131 | 24 | mod 6 | mixte | |
| RF000000000132 | 36 | mod 12 | mixte | |
| RF000000000133 | 48 | mod 6 | mixte | |
| RF000000000198 | 288 | mod 12 | sout | |
| RF000000000199 | 432 | mod 12 | sout | |
| RF000000000200 | 576 | mod 12 | sout | |
| RF000000000201 | 720 | mod 12 | sout | |
| RF000000000202 | 6 | mod 6 | mixte | |
| RF000000000203 | 48 | mod 12 | mixte | |
| RF000000000204 | 108 | mod 6 | mixte | |
| RF000000000205 | 108 | mod 12 | mixte | |
| RF000000000206 | 96 | mod 6 | mixte | |
| RF000000000207 | 96 | mod 12 | mixte | |
| RF000000000208 | 6 | mod 6 | mixte | |
| RF000000000209 | 48 | mod 12 | mixte | |
| RF000000000210 | 108 | mod 6 | mixte | |
| RF000000000211 | 108 | mod 12 | mixte | |
| RF000000000212 | 96 | mod 6 | mixte | |
| RF000000000213 | 96 | mod 12 | mixte | |
| RF000000000290 | 72 | mod 12 | sout | |
