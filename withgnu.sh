#!/usr/bin/env bash
set -euo pipefail

########################################
# Config
########################################

# Output formats (extensions, no dot)
# Example: OUT_FORMATS=("jpg" "webp")
OUT_FORMATS=("jpg" "webp" "avif")

# Default quality (if no per-format override)
QUALITY_DEFAULT=90      # decent baseline

# Optional per-format quality overrides:
# QUALITY_jpg=92
# QUALITY_webp=85
# QUALITY_avif=60
# (Uncomment / add as desired)
QUALITY_jpg=92          # a bit higher for fewer artifacts
QUALITY_webp=85         # WebP compresses well; 80 is often visually lossles
QUALITY_avif=60         # AVIF is much more efficient; lower numbers look good

########################################
# Cleanup old outputs in current dir
########################################
for ext in "${OUT_FORMATS[@]}"; do
  rm -rfv *."$ext"
done

# --- tool detection (ImageMagick preferred, fallback to GraphicsMagick) ---
if command -v magick >/dev/null 2>&1; then
  CONV_BIN="magick"
  CONV_IS_GM=0
elif command -v gm >/dev/null 2>&1; then
  CONV_BIN="gm"
  CONV_IS_GM=1
else
  echo "❌ Neither ImageMagick (magick) nor GraphicsMagick (gm) found. Aborting."
  exit 1
fi
echo "✅ Using ${CONV_BIN} for image conversion"

# --- decide parallelism: prefer getconf, fallback to nproc, then 4 ---
jobs="$(getconf _NPROCESSORS_ONLN 2>/dev/null || true)"
if [[ -z "${jobs}" ]]; then
  jobs="$(nproc 2>/dev/null || echo 4)"
fi
if ! [[ "${jobs}" =~ ^[0-9]+$ ]]; then
  jobs=4
fi

# --- ensure GNU parallel exists ---
if ! command -v parallel >/dev/null 2>&1; then
  echo "❌ GNU parallel not found. Use nognu.sh instead."
  exit 1
fi

# (Optional: run once to silence citation)
# parallel --citation >/dev/null 2>&1 || true

log_file="convert.log"
: > "$log_file"

# --- loop over output formats and run parallel for each ---
for ext in "${OUT_FORMATS[@]}"; do
  # Quality for this format
  v="QUALITY_${ext}"
  q="${!v:-$QUALITY_DEFAULT}"

  echo "▶ Converting PNG → .${ext} (quality=${q})"

  if [[ "$CONV_IS_GM" -eq 1 ]]; then
    # GraphicsMagick: gm convert -verbose in -strip -quality q out
    find . -type f -name "*.png" -print0 | sort -z | \
      parallel --bar --joblog "$log_file" -0 -j"${jobs}" '
        f={}
        out={.}.'"$ext"'

        echo "Converting $f → $out"

        if [ -e "$out" ]; then
          echo "  ↳ exists, skipping"
          exit 0
        fi

        '"$CONV_BIN"' convert -verbose "$f" -strip -quality '"$q"' "$out" &&
        touch -r "$f" "$out" || echo "  ↳ warning: failed to copy mtime for $out"
      '
  else
    # ImageMagick v7: magick -verbose in -strip -quality q out
    find . -type f -name "*.png" -print0 | sort -z | \
      parallel --bar --joblog "$log_file" -0 -j"${jobs}" '
        f={}
        out={.}.'"$ext"'

        echo "Converting $f → $out"

        if [ -e "$out" ]; then
          echo "  ↳ exists, skipping"
          exit 0
        fi

        '"$CONV_BIN"' -verbose "$f" -strip -quality '"$q"' "$out" &&
        touch -r "$f" "$out" || echo "  ↳ warning: failed to copy mtime for $out"
      '
  fi
done