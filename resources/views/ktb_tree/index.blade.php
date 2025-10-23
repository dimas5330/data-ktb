<x-layouts.app title="Pohon KTB">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Pohon KTB</h1>
            <div class="flex gap-2">
                <button id="toggleLayout" class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">
                    Toggle Layout
                </button>
                <button id="zoomIn" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Zoom In
                </button>
                <button id="zoomOut" class="rounded-lg bg-zinc-600 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700">
                    Zoom Out
                </button>
                <button id="resetZoom" class="rounded-lg bg-zinc-600 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700">
                    Reset
                </button>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div id="tree-container" style="height: 800px; overflow: auto; background: #ffffff;" class="dark:bg-zinc-800"></div>
        </div>

        <!-- Legend -->
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <h3 class="mb-3 font-semibold text-zinc-900 dark:text-white">Legend:</h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="h-4 w-4 rounded bg-green-500"></div>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Active</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-4 w-4 rounded bg-blue-500"></div>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Alumni</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-4 w-4 rounded bg-red-500"></div>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Inactive</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Layout:</span>
                    <span id="currentLayout" class="text-sm text-blue-600 dark:text-blue-400">Horizontal</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
        let currentZoom = 1;
        let treeData = null;
        let isHorizontal = true;

        fetch('{{ route("ktb-tree.data") }}')
            .then(response => response.json())
            .then(data => {
                treeData = data;
                renderTree(data, isHorizontal);
            })
            .catch(error => {
                console.error('Error loading tree data:', error);
                document.getElementById('tree-container').innerHTML =
                    '<div class="flex items-center justify-center h-full text-red-600">Error loading tree data</div>';
            });

        function elbowHorizontal(d) {
            // Connect from right edge of source circle to left edge of target circle
            const nodeRadius = 30;
            const sourceY = d.source.y + nodeRadius; // Right edge of source circle
            const targetY = d.target.y - nodeRadius; // Left edge of target circle

            // Calculate midpoint for clean elbow
            const midY = (sourceY + targetY) / 2;

            return `M${sourceY},${d.source.x}H${midY}V${d.target.x}H${targetY}`;
        }

        function elbowVertical(d) {
            // Connect from bottom edge of source circle to top edge of target circle
            const nodeRadius = 30;
            const sourceY = d.source.y + nodeRadius; // Bottom edge of source circle
            const targetY = d.target.y - nodeRadius; // Top edge of target circle

            // Calculate midpoint for clean elbow
            const midY = (sourceY + targetY) / 2;

            return `M${d.source.x},${sourceY}V${midY}H${d.target.x}V${targetY}`;
        }

        function renderTree(data, horizontal = true) {
            const container = document.getElementById('tree-container');
            container.innerHTML = '';

            const containerWidth = container.offsetWidth;
            const containerHeight = 800;
            const nodeSpacing = horizontal ? 180 : 350;
            const levelSpacing = horizontal ? 350 : 250;
            const treeWidth = horizontal ? levelSpacing * 6 : containerWidth - 100;
            const treeHeight = horizontal ? containerHeight - 100 : levelSpacing * 6;

            const svg = d3.select('#tree-container')
                .append('svg')
                .attr('width', '100%')
                .attr('height', containerHeight)
                .attr('viewBox', horizontal
                    ? `0 0 ${treeWidth + 200} ${containerHeight}`
                    : `0 0 ${containerWidth} ${treeHeight + 100}`)
                .attr('preserveAspectRatio', 'xMidYMid meet')
                .style('display', 'block')
                .style('margin', '0 auto');

            const g = svg.append('g')
                .attr('class', 'tree-group')
                .attr('transform', horizontal
                    ? `translate(80, ${containerHeight/2})`
                    : `translate(${containerWidth/2}, 50)`);

            const treeLayout = horizontal
                ? d3.tree().size([containerHeight - 100, treeWidth]).nodeSize([160, levelSpacing])
                : d3.tree().size([containerWidth - 200, treeHeight]).nodeSize([nodeSpacing, 180]);

            const root = d3.hierarchy({
                name: 'Root',
                id: 0,
                children: data
            });

            treeLayout(root);

            g.selectAll('.link')
                .data(root.links().filter(d => d.source.depth > 0))
                .join('path')
                .attr('class', 'link')
                .attr('fill', 'none')
                .attr('stroke', '#64748b')
                .attr('stroke-width', 2)
                .attr('d', horizontal ? elbowHorizontal : elbowVertical);

            const nodes = g.selectAll('.node')
                .data(root.descendants().filter(d => d.depth > 0))
                .join('g')
                .attr('class', 'node')
                .attr('transform', d => horizontal
                    ? `translate(${d.y},${d.x})`
                    : `translate(${d.x},${d.y})`)
                .style('cursor', 'pointer')
                .on('click', (event, d) => {
                    window.location.href = `/ktb-members/${d.data.id}`;
                });

            nodes.append('circle')
                .attr('r', 30)
                .attr('fill', d => {
                    if (d.data.status === 'active') return '#22c55e';
                    if (d.data.status === 'alumni') return '#3b82f6';
                    return '#ef4444';
                })
                .attr('stroke', '#fff')
                .attr('stroke-width', 3)
                .style('filter', 'drop-shadow(0 2px 4px rgba(0,0,0,0.1))');

            nodes.append('text')
                .attr('dy', '0.35em')
                .attr('text-anchor', 'middle')
                .style('fill', '#ffffff')
                .style('font-weight', '700')
                .style('font-size', '14px')
                .style('pointer-events', 'none')
                .text(d => {
                    const words = d.data.name.split(' ');
                    if (words.length >= 2) {
                        return (words[0][0] + words[1][0]).toUpperCase();
                    }
                    return d.data.name.substring(0, 2).toUpperCase();
                });

            if (horizontal) {
                // Name text
                nodes.append('text')
                    .attr('dy', '-40px')
                    .attr('dx', '0px')
                    .attr('text-anchor', 'middle')
                    .style('fill', '#000000')
                    .style('font-size', '13px')
                    .style('font-weight', '600')
                    .style('pointer-events', 'none')
                    .text(d => d.data.name);

                // Generation text
                nodes.append('text')
                    .attr('dy', '50px')
                    .attr('dx', '0px')
                    .attr('text-anchor', 'middle')
                    .style('fill', '#000000')
                    .style('font-size', '11px')
                    .style('pointer-events', 'none')
                    .text(d => `Gen ${d.data.generation}`);

                // Group text
                nodes.append('text')
                    .attr('dy', '65px')
                    .attr('dx', '0px')
                    .attr('text-anchor', 'middle')
                    .style('fill', '#7c3aed')
                    .style('font-size', '10px')
                    .style('font-weight', '600')
                    .style('pointer-events', 'none')
                    .text(d => d.data.group || 'No Group');

                // Mentees text
                nodes.append('text')
                    .attr('dy', '80px')
                    .attr('dx', '0px')
                    .attr('text-anchor', 'middle')
                    .style('fill', '#52525b')
                    .style('font-size', '10px')
                    .style('pointer-events', 'none')
                    .text(d => `${d.data.children?.length || 0} mentees`);
            } else {
                // Name text
                nodes.append('text')
                    .attr('dy', '50px')
                    .attr('text-anchor', 'middle')
                    .style('fill', '#000000')
                    .style('font-size', '13px')
                    .style('font-weight', '600')
                    .style('pointer-events', 'none')
                    .text(d => d.data.name);

                // Generation text
                nodes.append('text')
                    .attr('dy', '65px')
                    .attr('text-anchor', 'middle')
                    .style('fill', '#000000')
                    .style('font-size', '11px')
                    .style('pointer-events', 'none')
                    .text(d => `Gen ${d.data.generation}`);

                // Group text
                nodes.append('text')
                    .attr('dy', '80px')
                    .attr('text-anchor', 'middle')
                    .style('fill', '#7c3aed')
                    .style('font-size', '10px')
                    .style('font-weight', '600')
                    .style('pointer-events', 'none')
                    .text(d => d.data.group || 'No Group');

                // Mentees text
                nodes.append('text')
                    .attr('dy', '95px')
                    .attr('text-anchor', 'middle')
                    .style('fill', '#52525b')
                    .style('font-size', '10px')
                    .style('pointer-events', 'none')
                    .text(d => `${d.data.children?.length || 0} mentees`);
            }

            applyZoom(currentZoom);
            document.getElementById('currentLayout').textContent = horizontal ? 'Horizontal' : 'Vertical';

            setTimeout(() => {
                const container = document.getElementById('tree-container');
                if (horizontal) {
                    container.scrollLeft = (container.scrollWidth - container.clientWidth) / 2;
                } else {
                    container.scrollTop = 0;
                }
            }, 100);
        }

        function applyZoom(scale) {
            const svg = document.querySelector('#tree-container svg');
            const g = svg?.querySelector('.tree-group');
            if (g) {
                const transform = g.getAttribute('transform');
                const match = transform.match(/translate\(([^,]+),\s*([^)]+)\)/);
                if (match) {
                    const x = parseFloat(match[1]);
                    const y = parseFloat(match[2]);
                    g.setAttribute('transform', `translate(${x}, ${y}) scale(${scale})`);
                }
            }
        }

        document.getElementById('toggleLayout').onclick = function() {
            isHorizontal = !isHorizontal;
            currentZoom = 1;
            if (treeData) {
                renderTree(treeData, isHorizontal);
            }
        };

        document.getElementById('zoomIn').onclick = function() {
            currentZoom = Math.min(currentZoom + 0.2, 3);
            applyZoom(currentZoom);
        };

        document.getElementById('zoomOut').onclick = function() {
            currentZoom = Math.max(currentZoom - 0.2, 0.5);
            applyZoom(currentZoom);
        };

        document.getElementById('resetZoom').onclick = function() {
            currentZoom = 1;
            applyZoom(currentZoom);
        };
    </script>

    <style>
        #tree-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #tree-container svg {
            display: block;
        }
        .dark #tree-container {
            background: #27272a !important;
        }
        .dark #tree-container svg text {
            fill: #fafafa !important;
        }
        .dark #tree-container .link {
            stroke: #71717a !important;
        }
    </style>
</x-layouts.app>
